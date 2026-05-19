# Components

Components are named templates intended for reuse.

## Create Components

```php
use CommonPHP\UI\Component;

$badge = new Component('badge', 'components.badge', [
    'label' => 'New',
]);

$alert = Component::fromTemplate('alert', 'components.alert');
$icon = Component::fromFile('icon', __DIR__ . '/templates/components/icon.php');
```

Components expose both names:

- `componentName()` is the registry name.
- `name()` is the renderer template name.

For simple components, these can be the same.

## Register Components

```php
use CommonPHP\UI\ComponentRegistry;

$components = new ComponentRegistry([$badge]);

$components->set('alert', 'components.alert', [
    'type' => 'info',
]);
```

The registry exposes:

- `register()`;
- `set()`;
- `has()`;
- `get()`;
- `remove()`;
- `all()`;
- `names()`.

## Render Components

With `ViewFactory`:

```php
$ui->registerComponent(new Component('badge', 'components.badge'));

$html = $ui->renderComponent('badge', [
    'label' => 'Ready',
]);
```

Inside a native PHP template:

```php
<?= $component('badge', ['label' => 'Ready']) ?>
```

Component default data is merged with render data, and render data wins.

## Missing Components

Looking up an unknown component throws `InvalidComponentException`.
