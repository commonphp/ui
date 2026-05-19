# Components And Layouts

Directory structure:

```text
templates/
  components/
    badge.php
  layouts/
    app.php
  pages/
    dashboard.php
```

Component:

```php
<!-- templates/components/badge.php -->
<span class="badge"><?= $e($label) ?></span>
```

Page:

```php
<!-- templates/pages/dashboard.php -->
<h1><?= $e($title) ?></h1>
<?= $component('badge', ['label' => 'Ready']) ?>
```

Layout:

```php
<!-- templates/layouts/app.php -->
<main>
<?= $content ?>
</main>
```

Render:

```php
use CommonPHP\UI\Component;
use CommonPHP\UI\ViewFactory;

$ui = new ViewFactory(templatePaths: [__DIR__ . '/templates']);

$ui->registerComponent(new Component('badge', 'components.badge'));

$html = $ui->render(
    'pages.dashboard',
    ['title' => 'Dashboard'],
    'layouts.app',
);
```
