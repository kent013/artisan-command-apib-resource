# artisan-command-spectator-test

Artisan make command to generate HTTP testcases with OpenAPI and [spectator](https://github.com/hotmeteor/spectator).

## Disclaimer

This command generate only a scaffold. You need further implementation to test your API.

I'm using [api blueprint](https://apiblueprint.org/) for API specification.
Generating OpenAPI json with using [apib2swagger](https://github.com/kminami/apib2swagger).

While I'm not writing OpenAPI directly, some data such as operation ID is not natural. Perhaps it will cause a problem on this command.

## Installation

```
composer require --dev kent013/artisan-command-spectator-test
```

## Generate config file

```
php artisan vendor:publish --tag="spectator-test"
```

## Configuration
