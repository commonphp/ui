# Package Boundaries

CommonPHP UI owns lightweight rendering abstractions. It should stay small, predictable, and independent from application transport concerns.

## This Package Provides

- view objects;
- view data bags;
- template, layout, and component metadata;
- component registration and lookup;
- renderer contracts;
- a native PHP renderer;
- package-specific UI exceptions;
- a `ViewFactory` entry point.

## This Package Does Not Provide

- routing;
- HTTP request or response handling;
- form validation;
- session flashing;
- authorization;
- asset compilation;
- front-end build tooling;
- a global application container;
- a full template language.

## Related Packages

- `comphp/http` owns HTTP request and response objects.
- `comphp/router` owns route matching and dispatch.
- `comphp/validation` owns validation results and rules.
- `comphp/runtime` owns bootstrapping, lifecycle, and generic driver support.
- UI drivers such as Twig should live outside this package and implement `RendererInterface`.

## Design Rule

UI should describe what to render and which renderer can render it. It should not decide which route was matched, how responses are emitted, or how assets are built.
