# Motion Dots

## Typescript schema generation

Create, configure and run php script with content below in you project directory

```php
$schema = Schema::create()
  ->addMethods($methods);
$processor = new Processor($schema, '.');

$files_folder = "./static/api_schema/"; // any existing folder relative to current working directory

Generator::create()
  ->excludeSpaces('accounts', 'users') // default is 'system'
  ->setIsVerbose(false) // default is true
  ->generate($processor, $files_folder);
```