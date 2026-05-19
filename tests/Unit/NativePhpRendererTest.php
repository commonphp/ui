<?php

declare(strict_types=1);

namespace CommonPHP\UI\Tests\Unit;

use CommonPHP\UI\Component;
use CommonPHP\UI\ComponentRegistry;
use CommonPHP\UI\Contracts\ComponentRegistryInterface;
use CommonPHP\UI\Drivers\NativePhpRenderer;
use CommonPHP\UI\Exceptions\InvalidComponentException;
use CommonPHP\UI\Exceptions\RenderException;
use CommonPHP\UI\Exceptions\TemplateNotFoundException;
use CommonPHP\UI\Layout;
use CommonPHP\UI\Template;
use CommonPHP\UI\View;
use PHPUnit\Framework\TestCase;
use Stringable;

final class NativePhpRendererTest extends TestCase
{
    public function testConstructorAddsTemplatePathsAndCreatesAComponentRegistry(): void
    {
        $path = new class($this->templatePath()) implements Stringable {
            public function __construct(private readonly string $path)
            {
            }

            public function __toString(): string
            {
                return $this->path;
            }
        };
        $renderer = new NativePhpRenderer([$path, ' ', $this->templatePath()]);

        self::assertSame([$this->templatePath()], $renderer->paths());
        self::assertInstanceOf(ComponentRegistryInterface::class, $renderer->components());
    }

    public function testAddPathTrimsNormalizesDeduplicatesAndReturnsRenderer(): void
    {
        $renderer = new NativePhpRenderer();

        self::assertSame($renderer, $renderer->addPath(' '));
        self::assertSame($renderer, $renderer->addPath($this->templatePath() . DIRECTORY_SEPARATOR));
        self::assertSame($renderer, $renderer->addPath(str_replace('\\', '/', $this->templatePath())));

        self::assertSame([$this->templatePath()], $renderer->paths());
    }

    public function testItRendersTemplatesByDottedNameWithEscapedVariables(): void
    {
        $renderer = new NativePhpRenderer([$this->templatePath()]);

        self::assertSame(
            'Hello Ada &lt;Admin&gt;',
            trim($renderer->renderTemplate('pages.plain', ['name' => 'Ada <Admin>'])),
        );
    }

    public function testItRendersExplicitTemplateFilePaths(): void
    {
        $renderer = new NativePhpRenderer();
        $template = Template::file($this->templatePath() . '/pages/plain.php', ['name' => 'Ada']);

        self::assertSame('Hello Ada', trim($renderer->renderTemplate($template)));
    }

    public function testTemplateDataIsUsedAndRenderDataOverridesIt(): void
    {
        $renderer = new NativePhpRenderer([$this->templatePath()]);
        $template = new Template('pages.plain', ['name' => 'Default']);

        self::assertSame('Hello Default', trim($renderer->renderTemplate($template)));
        self::assertSame('Hello Override', trim($renderer->renderTemplate($template, ['name' => 'Override'])));
    }

    public function testItRendersViewsWithoutLayouts(): void
    {
        $renderer = new NativePhpRenderer([$this->templatePath()]);
        $view = new View('pages.without-layout', ['title' => 'Standalone']);

        self::assertSame('<section>Standalone</section>', trim($renderer->render($view)));
    }

    public function testItRendersViewsLayoutsAndRegisteredComponents(): void
    {
        $registry = new ComponentRegistry([
            new Component('badge', 'components.badge', ['label' => 'Default']),
        ]);
        $renderer = new NativePhpRenderer([$this->templatePath()], $registry);
        $view = new View(
            'pages.hello',
            ['title' => 'Hello <Ada>', 'label' => 'Ready'],
            new Layout('layouts.main', ['title' => 'Shell <Site>']),
        );

        self::assertSame(
            "<main data-title=\"Shell &lt;Site&gt;\">\n<h1>Hello &lt;Ada&gt;</h1>\n<span class=\"badge\">Ready</span>\n</main>",
            trim($renderer->render($view)),
        );
    }

    public function testLayoutsCanUseCustomContentKeys(): void
    {
        $renderer = new NativePhpRenderer([$this->templatePath()]);
        $view = new View(
            'pages.without-layout',
            ['title' => 'Inside'],
            new Layout('layouts.slot', contentKey: 'slot'),
        );

        self::assertSame(
            '<article><section>Inside</section></article>',
            preg_replace('/>\s+</', '><', trim($renderer->render($view))),
        );
    }

    public function testItCanRenderRegisteredAndDirectComponents(): void
    {
        $registry = new ComponentRegistry([
            new Component('badge', 'components.badge', ['label' => 'Default']),
        ]);
        $renderer = new NativePhpRenderer([$this->templatePath()], $registry);
        $direct = new Component('direct', 'components.badge', ['label' => 'Direct']);

        self::assertSame('<span class="badge">Default</span>', trim($renderer->renderComponent('badge')));
        self::assertSame('<span class="badge">Override</span>', trim($renderer->renderComponent('badge', [
            'label' => 'Override',
        ])));
        self::assertSame('<span class="badge">Direct</span>', trim($renderer->renderComponent($direct)));
    }

    public function testTemplateHelperVariablesDoNotGetOverwrittenByData(): void
    {
        $registry = new ComponentRegistry([
            new Component('badge', 'components.badge', ['label' => 'Badge']),
        ]);
        $renderer = new NativePhpRenderer([$this->templatePath()], $registry);

        $html = $renderer->renderTemplate('pages.helper-collision', [
            'title' => 'Title <Safe>',
            'component' => 'not callable',
            'renderer' => 'not renderer',
            'e' => 'not escape',
        ]);

        $html = preg_replace('/\s*\|\s*/', '|', trim($html));

        self::assertStringStartsWith('<span class="badge">Badge</span>|Title &lt;Safe&gt;|', $html);
        self::assertStringEndsWith(NativePhpRenderer::class, $html);
    }

    public function testItThrowsForMissingTemplates(): void
    {
        $renderer = new NativePhpRenderer([$this->templatePath()]);

        $this->expectException(TemplateNotFoundException::class);
        $this->expectExceptionMessage('missing.template');

        $renderer->renderTemplate('missing.template');
    }

    public function testItThrowsForMissingRegisteredComponents(): void
    {
        $renderer = new NativePhpRenderer([$this->templatePath()]);

        $this->expectException(InvalidComponentException::class);
        $this->expectExceptionMessage('Component "missing" is not registered.');

        $renderer->renderComponent('missing');
    }

    public function testUIExceptionsInsideTemplatesBubbleOut(): void
    {
        $renderer = new NativePhpRenderer([$this->templatePath()]);

        $this->expectException(InvalidComponentException::class);

        $renderer->renderTemplate('pages.missing-component');
    }

    public function testItWrapsTemplateRuntimeFailuresAndCleansOutputBuffers(): void
    {
        $renderer = new NativePhpRenderer([$this->templatePath()]);
        $level = ob_get_level();

        try {
            $renderer->renderTemplate('pages.explode');
            self::fail('Expected render failures to be wrapped.');
        } catch (RenderException $exception) {
            self::assertStringContainsString('pages.explode', $exception->getMessage());
            self::assertSame('Template exploded.', $exception->getPrevious()?->getMessage());
            self::assertSame($level, ob_get_level());
        }
    }

    private function templatePath(): string
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, dirname(__DIR__) . '/Fixtures/templates');
    }
}
