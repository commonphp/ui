<?php

declare(strict_types=1);

namespace CommonPHP\UI\Tests\Unit;

use CommonPHP\UI\Component;
use CommonPHP\UI\ComponentRegistry;
use CommonPHP\UI\Contracts\ComponentRegistryInterface;
use CommonPHP\UI\Contracts\RendererInterface;
use CommonPHP\UI\Drivers\NativePhpRenderer;
use CommonPHP\UI\Template;
use CommonPHP\UI\Tests\Fixtures\RecordingRenderer;
use CommonPHP\UI\View;
use CommonPHP\UI\ViewFactory;
use PHPUnit\Framework\TestCase;

final class ViewFactoryTest extends TestCase
{
    public function testItCreatesViewTemplateFileLayoutAndComponentObjects(): void
    {
        $registry = new ComponentRegistry();
        $factory = new ViewFactory(components: $registry);

        $view = $factory->view('pages.profile', ['name' => 'Ada'], 'layouts.main');
        $template = $factory->template('pages.account', ['title' => 'Account']);
        $file = $factory->file('pages/account.php', ['name' => 'Grace'], 'account');
        $layout = $factory->layout('layouts.shell', ['title' => 'Shell'], contentKey: 'slot');
        $component = $factory->component('badge', 'components.badge', ['label' => 'Ready']);

        self::assertSame('pages.profile', $view->template()->name());
        self::assertSame('Ada', $view->data()->get('name'));
        self::assertSame('layouts.main', $view->layout()?->name());
        self::assertSame('pages.account', $template->name());
        self::assertSame('Account', $template->data()->get('title'));
        self::assertSame('account', $file->name());
        self::assertSame('Grace', $file->data()->get('name'));
        self::assertSame('slot', $layout->contentKey());
        self::assertSame('badge', $component->componentName());
        self::assertSame($registry, $factory->components());
    }

    public function testItCreatesANativeRendererByDefaultWithSharedComponents(): void
    {
        $factory = new ViewFactory(templatePaths: [$this->templatePath()]);

        self::assertFalse($factory->hasDriver());
        self::assertInstanceOf(NativePhpRenderer::class, $factory->renderer());
        self::assertSame($factory->components(), $factory->renderer()->components());
    }

    public function testItRegistersComponentsAndRendersThroughDefaultRenderer(): void
    {
        $factory = new ViewFactory(templatePaths: [$this->templatePath()]);

        self::assertSame($factory, $factory->registerComponent(new Component('badge', 'components.badge')));

        $html = $factory->render(
            'pages.hello',
            ['title' => 'Hello', 'label' => 'Factory'],
            $factory->layout('layouts.main', ['title' => 'Site']),
        );

        self::assertStringContainsString('<h1>Hello</h1>', $html);
        self::assertStringContainsString('<span class="badge">Factory</span>', $html);
    }

    public function testItCanRenderExistingViewsTemplatesAndComponents(): void
    {
        $factory = new ViewFactory(templatePaths: [$this->templatePath()]);
        $factory->registerComponent(new Component('badge', 'components.badge', ['label' => 'Default']));
        $view = new View('pages.without-layout', ['title' => 'Existing']);

        self::assertSame('<section>Existing</section>', trim($factory->render($view)));
        self::assertSame('Hello Template', trim($factory->renderTemplate(new Template('pages.plain'), [
            'name' => 'Template',
        ])));
        self::assertSame('<span class="badge">Default</span>', trim($factory->renderComponent('badge')));
    }

    public function testUseRendererSwapsTheDirectRenderer(): void
    {
        $renderer = new RecordingRenderer();
        $factory = new ViewFactory();

        self::assertSame($factory, $factory->useRenderer($renderer));
        self::assertSame($renderer, $factory->renderer());
        self::assertSame('rendered:pages.custom', $factory->render('pages.custom'));
        self::assertSame('template:pages.partial', $factory->renderTemplate('pages.partial'));
        self::assertSame('component:badge', $factory->renderComponent('badge'));
        self::assertSame(['render:pages.custom', 'template:pages.partial', 'component:badge'], $renderer->calls);
    }

    public function testRuntimeDriverIntegrationCanSwapRendererClasses(): void
    {
        $factory = new ViewFactory();
        $factory->setDriver(NativePhpRenderer::class, [
            'templatePaths' => [$this->templatePath()],
        ]);

        self::assertTrue($factory->hasDriver());
        self::assertInstanceOf(RendererInterface::class, $factory->renderer());
        self::assertSame('<span class="badge">Driver</span>', trim($factory->renderTemplate('components.badge', [
            'label' => 'Driver',
        ])));
    }

    public function testRuntimeDriverTakesPrecedenceOverDirectRenderer(): void
    {
        $directRenderer = new RecordingRenderer();
        $factory = new ViewFactory($directRenderer);
        $factory->setDriver(NativePhpRenderer::class, [
            'templatePaths' => [$this->templatePath()],
        ]);

        self::assertNotSame($directRenderer, $factory->renderer());
        self::assertSame('Hello Driver', trim($factory->renderTemplate('pages.plain', [
            'name' => 'Driver',
        ])));
        self::assertSame([], $directRenderer->calls);
    }

    private function templatePath(): string
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, dirname(__DIR__) . '/Fixtures/templates');
    }
}
