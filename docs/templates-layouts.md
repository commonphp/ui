# Templates And Layouts

Templates describe what renderer target to use. Layouts are templates that receive rendered view content.

## Templates

```php
use CommonPHP\UI\Template;

$template = new Template('pages.profile', [
    'title' => 'Profile',
]);

$same = Template::named('pages.profile');
$file = Template::file(__DIR__ . '/templates/pages/profile.php', name: 'profile');
```

Templates expose:

- `name()`;
- `path()`;
- `data()`;
- `withData()`;
- `with()`.

`withData()` and `with()` return clones.

## Template Names

The native renderer accepts names like:

- `pages.profile`;
- `pages/profile`;
- `pages/profile.php`;
- an explicit file-backed `Template`.

Dotted names are converted into paths when the native renderer searches template directories.

## Layouts

```php
use CommonPHP\UI\Layout;

$layout = new Layout('layouts.main', [
    'title' => 'Application',
]);
```

By default, rendered view content is assigned to `content`:

```php
<!-- templates/layouts/main.php -->
<main>
<?= $content ?>
</main>
```

Use a custom key when a layout expects another variable:

```php
$layout = new Layout('layouts.main', contentKey: 'slot');
```

```php
<!-- templates/layouts/main.php -->
<main>
<?= $slot ?>
</main>
```

## Data Merge Order

For templates, template data is used as defaults and render data overrides it.

For layouts, view render data is used first, then layout data can override it. The rendered body is assigned to the layout content key last.
