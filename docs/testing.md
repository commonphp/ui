# Testing And QA

CommonPHP UI includes a package-local PHPUnit configuration and unit tests.

## Install Dependencies

From the package directory:

```bash
composer install
```

From the monorepo, the root `vendor` directory can also satisfy the test suite because `tests/bootstrap.php` checks both package and workspace autoloaders.

## Run PHPUnit

From the monorepo root:

```bash
vendor/bin/phpunit -c package/ui/phpunit.xml.dist
```

From `package/ui`:

```bash
../../vendor/bin/phpunit -c phpunit.xml.dist
```

On Windows from `package/ui`:

```bat
..\..\vendor\bin\phpunit.bat --configuration phpunit.xml.dist
```

## Current Test Coverage

The unit suite covers:

- `ViewData` construction, direct and nested lookup, direct-key precedence, mutation, clone helpers, array access, iteration, counting, JSON serialization, and merging another `ViewData`;
- `Template` factories, file-backed paths, data, clone helpers, and blank-name rejection;
- `Layout` content keys, file-backed layouts, clone helpers, and invalid content key rejection;
- `Component` constructors and factories, component names, template names, paths, and data;
- `View` construction from strings and objects, factory creation, data, layouts, and clone helpers;
- `ComponentRegistry` construction, registration, shorthand setup, lookup, removal, listing, and exception paths;
- `AbstractRenderer` default driver names and helper normalization behavior;
- `NativePhpRenderer` path registration, template resolution, explicit file templates, escaping, layouts, custom content keys, registered and direct components, helper collision behavior, missing templates, missing components, template exception wrapping, UI exception bubbling, and output buffer cleanup;
- `ViewFactory` object factories, default native renderer setup, shared component registry, component registration, direct renderer swapping, render delegation, runtime driver integration, and driver precedence;
- package exception factory messages and previous throwable preservation.

## Manual Review Areas

Manual review should still cover downstream integrations that turn rendered strings into HTTP responses, wire third-party template engines, or inject application-specific helpers.
