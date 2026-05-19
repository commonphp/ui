# CommonPHP UI

CommonPHP UI provides driver-based UI rendering and reusable component support for CommonPHP applications. It defines the common structure for rendering global components, layouts, and interface elements through interchangeable rendering drivers.

The package keeps UI composition consistent without locking applications to one rendering engine or template implementation.

## Requirements

- PHP `^8.5`
- `comphp/runtime:^0.3`

## Installation

Once this package is available through your Composer repositories, install it with:

```bash
composer require comphp/ui
```

## Usage

```php
<?php

// TODO: Write usage
```

## Package Notes

This package should provide driver-based UI rendering and global component support without locking the system to a specific renderer. Twig, PHP templates, or other rendering engines should be implemented as UI drivers.

## Error Handling

Missing templates, invalid components, renderer failures, and driver errors should throw CommonPHP UI exceptions.

## Documentation

- [Documentation index](docs/index.md)
- [Usage](docs/usage.md)
- [Testing](TESTING.md)
- [Contributing](CONTRIBUTING.md)
- [Security](SECURITY.md)

## License

MIT. See [LICENSE.md](LICENSE.md).
