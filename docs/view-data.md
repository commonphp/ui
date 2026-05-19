# View Data

`ViewData` is a small data bag used by views, templates, layouts, components, and renderers.

## Create Data

```php
use CommonPHP\UI\ViewData;

$data = new ViewData([
    'title' => 'Dashboard',
]);

$same = ViewData::from($data);
$fromArray = ViewData::from(['title' => 'Dashboard']);
$empty = ViewData::from();
```

## Read Data

```php
$data->get('title');
$data->get('missing', 'fallback');
$data->has('title');
```

Dot notation can read nested arrays:

```php
$data = new ViewData([
    'profile' => [
        'name' => 'Ada',
    ],
]);

$data->get('profile.name'); // Ada
```

Direct keys take precedence over dotted lookup:

```php
$data = new ViewData([
    'profile.name' => 'Direct',
    'profile' => ['name' => 'Nested'],
]);

$data->get('profile.name'); // Direct
```

## Mutate Data

```php
$data
    ->set('title', 'Reports')
    ->merge(['status' => 'ready'])
    ->remove('status');

$data->replace(['fresh' => true]);
$data->clear();
```

## Clone Data

```php
$changed = $data
    ->with('title', 'Reports')
    ->withMerged(['status' => 'ready'])
    ->without('debug');
```

## Array And Iterator Support

`ViewData` implements `ArrayAccess`, `Countable`, `IteratorAggregate`, and `JsonSerializable`.

```php
$data['title'] = 'Dashboard';
isset($data['title']);
unset($data['title']);

foreach ($data as $key => $value) {
    // ...
}

count($data);
$data->jsonSerialize();
```
