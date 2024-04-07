# artisan-command-spectator-test

Artisan command to generate apib snippet.

## Disclaimer

Not completed.
Only processable Enums and JsonResource.

## Installation

```
composer require --dev kent013/artisan-command-spectator-test
```

## Example 

```
generate:apib App/Enums/Region --file
generate:apib App/Http/Resources/SomeObjectResource --file
```

use `--output-directory` option to specify dir. Otherwise it outputs into `tmp/apib`

```[]
generate:apib App/Enums/Region --stdout
generate:apib App/Http/Resources/SomeObjectResource --stdout
```