<?php

declare(strict_types=1);

namespace CommonPHP\UI\Tests\Unit;

use CommonPHP\UI\Component;
use CommonPHP\UI\ComponentRegistry;
use CommonPHP\UI\Contracts\ComponentInterface;
use CommonPHP\UI\Contracts\LayoutInterface;
use CommonPHP\UI\Contracts\TemplateInterface;
use CommonPHP\UI\Exceptions\InvalidComponentException;
use CommonPHP\UI\Exceptions\UIException;
use CommonPHP\UI\Layout;
use CommonPHP\UI\Template;
use CommonPHP\UI\View;
use CommonPHP\UI\ViewData;
use PHPUnit\Framework\TestCase;

final class ViewObjectsTest extends TestCase
{
    public function testTemplatesCanBeNamedOrFileBackedAndCarryData(): void
    {
        $template = Template::named('pages.profile', ['title' => 'Profile']);
        $file = Template::file('templates/pages/profile.php', ['name' => 'Ada'], 'profile');

        self::assertInstanceOf(TemplateInterface::class, $template);
        self::assertSame('pages.profile', $template->name());
        self::assertNull($template->path());
        self::assertSame('Profile', $template->data()->get('title'));
        self::assertSame('profile', $file->name());
        self::assertSame(
            implode(DIRECTORY_SEPARATOR, ['templates', 'pages', 'profile.php']),
            $file->path(),
        );
        self::assertSame('Ada', $file->data()->get('name'));
    }

    public function testTemplateWithMethodsReturnAdjustedCopies(): void
    {
        $template = new Template('pages.profile', ['name' => 'Ada']);
        $changed = $template->with('name', 'Grace')->withData(['title' => 'Profile']);

        self::assertSame('Ada', $template->data()->get('name'));
        self::assertSame(['title' => 'Profile'], $changed->data()->all());
    }

    public function testTemplatesRejectBlankNames(): void
    {
        $this->expectException(UIException::class);
        $this->expectExceptionMessage('Template names cannot be empty.');

        new Template('   ');
    }

    public function testLayoutsExtendTemplatesAndCustomizeContentKey(): void
    {
        $layout = new Layout('layouts.main', ['title' => 'Site'], contentKey: 'slot');
        $file = Layout::file('layouts/main.php', ['title' => 'File']);
        $changed = $layout->withContentKey('content');

        self::assertInstanceOf(LayoutInterface::class, $layout);
        self::assertSame('slot', $layout->contentKey());
        self::assertSame('content', $changed->contentKey());
        self::assertSame('layouts/main.php', str_replace('\\', '/', (string) $file->path()));
    }

    public function testLayoutsRejectBlankContentKeys(): void
    {
        $this->expectException(UIException::class);
        $this->expectExceptionMessage('Layout content keys cannot be empty.');

        new Layout('layouts.main', contentKey: '   ');
    }

    public function testComponentsExposeComponentNamesTemplateNamesPathsAndFactories(): void
    {
        $component = new Component('badge', 'components.badge', ['label' => 'Ready']);
        $fromTemplate = Component::fromTemplate('alert', 'components.alert', ['type' => 'info']);
        $file = Component::file('components/icon.php', ['name' => 'save'], 'icon');
        $fromFile = Component::fromFile('avatar', 'components/avatar.php', ['name' => 'Ada']);

        self::assertInstanceOf(ComponentInterface::class, $component);
        self::assertSame('badge', $component->componentName());
        self::assertSame('components.badge', $component->name());
        self::assertSame('Ready', $component->data()->get('label'));
        self::assertSame('alert', $fromTemplate->componentName());
        self::assertSame('components.alert', $fromTemplate->name());
        self::assertSame('icon', $file->componentName());
        self::assertSame('components/icon.php', str_replace('\\', '/', (string) $file->path()));
        self::assertSame('avatar', $fromFile->componentName());
        self::assertSame('components/avatar.php', str_replace('\\', '/', (string) $fromFile->path()));
    }

    public function testViewsCanBeCreatedFromStringsObjectsAndFactories(): void
    {
        $template = new Template('pages.profile', ['title' => 'Default']);
        $layout = new Layout('layouts.main');
        $view = View::make($template, ['name' => 'Ada'], $layout);
        $stringView = new View('pages.account', layout: 'layouts.shell');

        self::assertSame($template, $view->template());
        self::assertSame('Ada', $view->data()->get('name'));
        self::assertSame($layout, $view->layout());
        self::assertSame('pages.account', $stringView->template()->name());
        self::assertSame('layouts.shell', $stringView->layout()?->name());
    }

    public function testViewWithMethodsReturnAdjustedCopies(): void
    {
        $view = new View('pages.profile', ['name' => 'Ada'], 'layouts.main');
        $changed = $view
            ->with('name', 'Grace')
            ->withData(new ViewData(['title' => 'Account']))
            ->withTemplate('pages.account')
            ->withoutLayout();

        self::assertSame('Ada', $view->data()->get('name'));
        self::assertSame('pages.profile', $view->template()->name());
        self::assertNotNull($view->layout());

        self::assertSame(['title' => 'Account'], $changed->data()->all());
        self::assertSame('pages.account', $changed->template()->name());
        self::assertNull($changed->layout());
    }

    public function testViewWithLayoutCanAcceptNullStringsAndLayoutObjects(): void
    {
        $view = new View('pages.profile');
        $withString = $view->withLayout('layouts.main');
        $withObject = $view->withLayout(new Layout('layouts.shell'));
        $without = $withString->withLayout(null);

        self::assertSame('layouts.main', $withString->layout()?->name());
        self::assertSame('layouts.shell', $withObject->layout()?->name());
        self::assertNull($without->layout());
    }

    public function testComponentRegistryRegistersRetrievesSetsRemovesAndListsComponents(): void
    {
        $badge = new Component('badge', 'components.badge');
        $registry = new ComponentRegistry([$badge]);

        self::assertTrue($registry->has(' badge '));
        self::assertSame($badge, $registry->get('badge'));

        self::assertSame($registry, $registry->set('alert', 'components.alert', ['type' => 'info']));
        self::assertSame('components.alert', $registry->get('alert')->name());
        self::assertSame('info', $registry->get('alert')->data()->get('type'));

        $custom = new Component('toast', 'components.toast');
        self::assertSame($registry, $registry->set('ignored', $custom));
        self::assertSame($custom, $registry->get('toast'));
        self::assertSame(['badge', 'alert', 'toast'], $registry->names());
        self::assertSame(['badge', 'alert', 'toast'], array_keys($registry->all()));

        self::assertSame($registry, $registry->remove('alert'));
        self::assertFalse($registry->has('alert'));
    }

    public function testComponentRegistryRejectsBlankAndMissingComponents(): void
    {
        $registry = new ComponentRegistry();

        try {
            $registry->has(' ');
            self::fail('Expected blank component names to be rejected.');
        } catch (InvalidComponentException $exception) {
            self::assertSame('Component names cannot be empty.', $exception->getMessage());
        }

        $this->expectException(InvalidComponentException::class);
        $this->expectExceptionMessage('Component "missing" is not registered.');

        $registry->get('missing');
    }
}
