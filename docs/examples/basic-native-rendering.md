# Basic Native Rendering

Directory structure:

```text
templates/
  pages/
    home.php
```

Template:

```php
<!-- templates/pages/home.php -->
<h1><?= $e($title) ?></h1>
```

Render:

```php
use CommonPHP\UI\ViewFactory;

$ui = new ViewFactory(templatePaths: [__DIR__ . '/templates']);

$html = $ui->render('pages.home', [
    'title' => 'Home',
]);
```

`pages.home` resolves to `templates/pages/home.php`.
