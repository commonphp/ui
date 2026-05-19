<?php

declare(strict_types=1);

namespace CommonPHP\UI\Tests\Unit;

use CommonPHP\UI\Component;
use CommonPHP\UI\Template;
use CommonPHP\UI\Tests\Fixtures\ExposedRenderer;
use CommonPHP\UI\ViewData;
use PHPUnit\Framework\TestCase;

final class AbstractRendererTest extends TestCase
{
    public function testItProvidesDefaultDriverName(): void
    {
        $renderer = new ExposedRenderer();

        self::assertSame(ExposedRenderer::class, $renderer->getName());
    }

    public function testItNormalizesStringTemplatesAndPreservesTemplateObjects(): void
    {
        $renderer = new ExposedRenderer();
        $template = new Template('pages.profile');

        self::assertSame('pages.account', $renderer->exposeTemplate('pages.account')->name());
        self::assertSame($template, $renderer->exposeTemplate($template));
    }

    public function testItNormalizesStringComponentsAndPreservesComponentObjects(): void
    {
        $renderer = new ExposedRenderer();
        $component = new Component('badge');

        self::assertSame('alert', $renderer->exposeComponent('alert')->componentName());
        self::assertSame($component, $renderer->exposeComponent($component));
    }

    public function testItNormalizesViewDataAndMergesDefaultsWithOverrides(): void
    {
        $renderer = new ExposedRenderer();
        $data = new ViewData(['title' => 'Existing']);
        $merged = $renderer->exposeMergedData($data, ['title' => 'Override', 'name' => 'Ada']);

        self::assertSame($data, $renderer->exposeData($data));
        self::assertSame(['title' => 'Array'], $renderer->exposeData(['title' => 'Array'])->all());
        self::assertSame(['title' => 'Override', 'name' => 'Ada'], $merged->all());
        self::assertSame(['title' => 'Existing'], $data->all());
    }

    public function testExposedRendererImplementsRenderOperations(): void
    {
        $renderer = new ExposedRenderer();

        self::assertSame('template:pages.profile:{"title":"Profile"}', $renderer->renderTemplate(
            new Template('pages.profile', ['title' => 'Default']),
            ['title' => 'Profile'],
        ));
        self::assertSame('component:badge', $renderer->renderComponent(new Component('badge')));
    }
}
