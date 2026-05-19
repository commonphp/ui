# Error Handling

All package-specific exceptions extend `UIException`.

## Exception Types

`UIException`

Base exception for this package.

`TemplateNotFoundException`

Thrown when a renderer cannot resolve a template.

`InvalidComponentException`

Thrown for blank component names or missing component registrations.

`RenderException`

Thrown when template execution fails unexpectedly.

`RendererDriverException`

Reserved for renderer driver setup or operation failures.

## Expected Failures

Expected UI failures should use package exceptions:

```php
throw TemplateNotFoundException::forTemplate($name, $searchedPaths);
```

```php
throw InvalidComponentException::notFound($name);
```

## Unexpected Template Failures

The native PHP renderer wraps unexpected throwables from included templates in `RenderException` and preserves the previous throwable.

```php
try {
    $html = $renderer->renderTemplate('pages.profile');
} catch (RenderException $exception) {
    $previous = $exception->getPrevious();
}
```

Existing `UIException` instances thrown inside templates bubble out unchanged.

## Missing Template Diagnostics

`TemplateNotFoundException::forTemplate()` can include searched paths:

```php
TemplateNotFoundException::forTemplate('pages.profile', [
    '/app/templates/pages/profile.php',
]);
```

This helps debug search-path and naming mistakes without coupling the package to a logger.
