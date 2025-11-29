[![GitHub Workflow Status][ico-tests]][link-tests]
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

------

Reusable polymorphic key mapping for Laravel packages. Provides a central registry to manage which primary key column (id, uuid, ulid) each model uses in polymorphic relationships.

## Requirements

> **Requires [PHP 8.4+](https://php.net/releases/)** and Laravel 12+

## Installation

```bash
composer require cline/morpheus
```

## Documentation

- **[Migrations](cookbooks/migrations.php)** - Blueprint macros for polymorphic columns
- **[Basic Usage](cookbooks/basic-usage.php)** - Core registry operations
- **[Strict Enforcement](cookbooks/strict-enforcement.php)** - Require all models to be mapped
- **[Config-Based Setup](cookbooks/config-based-setup.php)** - Configure via config files
- **[Package Integration](cookbooks/package-integration.php)** - Integrate into your own packages
- **[Testing](cookbooks/testing-with-morpheus.php)** - Testing patterns and best practices

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please use the [GitHub security reporting form][link-security] rather than the issue queue.

## Credits

- [Brian Faust][link-maintainer]
- [All Contributors][link-contributors]

## License

The MIT License. Please see [License File](LICENSE.md) for more information.

[ico-tests]: https://github.com/faustbrian/morpheus/actions/workflows/quality-assurance.yaml/badge.svg
[ico-version]: https://img.shields.io/packagist/v/cline/morpheus.svg
[ico-license]: https://img.shields.io/badge/License-MIT-green.svg
[ico-downloads]: https://img.shields.io/packagist/dt/cline/morpheus.svg

[link-tests]: https://github.com/faustbrian/morpheus/actions
[link-packagist]: https://packagist.org/packages/cline/morpheus
[link-downloads]: https://packagist.org/packages/cline/morpheus
[link-security]: https://github.com/faustbrian/morpheus/security
[link-maintainer]: https://github.com/faustbrian
[link-contributors]: ../../contributors
