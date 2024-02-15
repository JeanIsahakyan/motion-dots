# Motion Dots

## Typescript schema generation

### Conventions

If you are going to use typescript api schema generation you have to follow these principles:
- Response class names must be unique
- Enum names used in parameters and response classes must be unique

### Structure

```
api-schema
├── methods
├── enums
└── responses
```

### Generation

Run php script with content below in you project directory

```php
$schema = Schema::create()
  ->addMethods($methods);
$processor = new Processor($schema, '.');

$files_folder = "./static/api-schema/"; // any existing folder relative to current working directory

Generator::create()
  ->excludeSpaces('accounts', 'users') // default is 'system'
  ->setIsVerbose(false) // default is true
  ->setFilesPath('./static/api-schema') // default is './api-schema'
  ->generate($processor);
```