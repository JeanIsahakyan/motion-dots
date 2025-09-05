# API Reference

## Table of Contents

- [Core Classes](#core-classes)
- [Method System](#method-system)
- [Type System](#type-system)
- [Response System](#response-system)
- [TypeScript Generation](#typescript-generation)
- [Error Codes](#error-codes)
- [Constants](#constants)

## Core Classes

### Processor

**Namespace**: `MotionDots\Process`  
**File**: `src/Process/Processor.php`

Main orchestrator for handling API requests and method invocation.

#### Constructor

```php
public function __construct(Schema $schema, string $separator = '.', bool $disable_system_methods = false)
```

**Parameters:**
- `$schema` (Schema): Schema instance containing registered methods
- `$separator` (string): Method separator (default: '.')
- `$disable_system_methods` (bool): Disable built-in system methods (default: false)

#### Methods

##### `invokeProcess(string $method, array &$params = []): array`

Invokes a method and returns structured response.

**Parameters:**
- `$method` (string): Method name in format "methodName.actionName"
- `$params` (array): Request parameters (passed by reference)

**Returns:** Array containing response data

**Throws:** `ErrorException` for various error conditions

##### `execute(string $method, array &$params = []): array`

Executes method with error handling.

**Parameters:**
- `$method` (string): Method name
- `$params` (array): Request parameters (passed by reference)

**Returns:** Array containing either response or error data

##### `getContext(): Context`

Returns the shared context instance.

**Returns:** Context instance

##### `getSeparator(): string`

Returns the method separator string.

**Returns:** String separator

### Schema

**Namespace**: `MotionDots\Schema`  
**File**: `src/Schema/Schema.php`

Manages method registration and provides method lookup capabilities.

#### Static Methods

##### `create(): self`

Creates a new Schema instance.

**Returns:** New Schema instance

#### Instance Methods

##### `addMethod(AbstractMethod $method): self`

Adds a single method to the schema.

**Parameters:**
- `$method` (AbstractMethod): Method instance to register

**Returns:** Self for method chaining

**Throws:** `ErrorException` if method already exists

##### `addMethods(array $methods): self`

Adds multiple methods to the schema.

**Parameters:**
- `$methods` (array): Array of AbstractMethod instances

**Returns:** Self for method chaining

##### `methodExists(string $method): bool`

Checks if a method is registered.

**Parameters:**
- `$method` (string): Method name to check

**Returns:** True if method exists, false otherwise

##### `getMethod(string $method): AbstractMethod`

Retrieves a registered method.

**Parameters:**
- `$method` (string): Method name

**Returns:** AbstractMethod instance

##### `getMethods(): array`

Returns all registered methods.

**Returns:** Array of AbstractMethod instances

### Context

**Namespace**: `MotionDots\Process`  
**File**: `src/Process/Context.php`

Shared data container accessible across methods and types.

#### Constructor

```php
public function __construct()
```

Creates a new context instance.

#### Methods

##### `get(string $field)`

Retrieves a value from the context.

**Parameters:**
- `$field` (string): Field name to retrieve

**Returns:** The stored value

**Throws:** `ErrorException` if field doesn't exist

##### `set(string $field, $value): self`

Sets a value in the context.

**Parameters:**
- `$field` (string): Field name
- `$value` (mixed): Value to store

**Returns:** Self for method chaining

##### `setMany(array $fields): void`

Sets multiple values in the context.

**Parameters:**
- `$fields` (array): Associative array of field => value pairs

##### `getAll(): array`

Returns all context data.

**Returns:** Array of all stored data

### MethodProcessor

**Namespace**: `MotionDots\Process`  
**File**: `src/Process/MethodProcessor.php`

Handles individual method execution with parameter parsing.

#### Constructor

```php
public function __construct(AbstractMethod &$method, string &$action, array &$params = [])
```

**Parameters:**
- `$method` (AbstractMethod): Method instance (passed by reference)
- `$action` (string): Action name (passed by reference)
- `$params` (array): Request parameters (passed by reference)

**Throws:** `ErrorException` for reflection errors

#### Static Methods

##### `create(AbstractMethod &$method, string &$action, array $params = []): self`

Factory method to create a MethodProcessor instance.

**Parameters:**
- `$method` (AbstractMethod): Method instance (passed by reference)
- `$action` (string): Action name (passed by reference)
- `$params` (array): Request parameters

**Returns:** New MethodProcessor instance

**Throws:** `ErrorException` for reflection errors

#### Instance Methods

##### `invoke()`

Invokes the target method with parsed parameters.

**Returns:** Method execution result

### ParamParser

**Namespace**: `MotionDots\Process`  
**File**: `src/Process/ParamParser.php`

Validates and parses method parameters using the type system.

#### Constructor

```php
public function __construct(\ReflectionMethod &$reflection, Context &$context, &$params)
```

**Parameters:**
- `$reflection` (ReflectionMethod): Method reflection (passed by reference)
- `$context` (Context): Request context (passed by reference)
- `$params` (array): Request parameters (passed by reference)

#### Methods

##### `getParams()`

Returns the processed parameters ready for method invocation.

**Returns:** Array of processed parameters

### ResponseBuilder

**Namespace**: `MotionDots\Process`  
**File**: `src/Process/ResponseBuilder.php`

Converts method responses to structured JSON format.

#### Static Methods

##### `build($response): array`

Builds a structured response from method output.

**Parameters:**
- `$response` (mixed): Method response

**Returns:** Structured response array

##### `tryBuild($response)`

Recursively processes response data for JSON serialization.

**Parameters:**
- `$response` (mixed): Response data to process

**Returns:** Processed response data

## Method System

### AbstractMethod

**Namespace**: `MotionDots\Method`  
**File**: `src/Method/AbstractMethod.php`

Base class for all API method classes.

#### Properties

##### `$context`

```php
public $context;
```

The shared context instance.

#### Methods

##### `__setContext(Context &$context)`

Sets the context instance for the method.

**Parameters:**
- `$context` (Context): Context instance (passed by reference)

##### `__call($name, $arguments): ResponseInterface`

Magic method for dynamic method invocation.

**Parameters:**
- `$name` (string): Method name to call
- `$arguments` (array): Method arguments

**Returns:** ResponseInterface implementation

##### `__toString(): string`

Returns the method name for schema registration.

**Returns:** Method name (class name in camelCase)

##### `__actionExists(string $action): bool`

Checks if an action exists in the method class.

**Parameters:**
- `$action` (string): Action name to check

**Returns:** True if action exists, false otherwise

### MethodInterface

**Namespace**: `MotionDots\Method`  
**File**: `src/Method/MethodInterface.php`

Interface defining the contract for method classes.

#### Methods

##### `__call($name, $arguments): ResponseInterface`

Defines the magic method signature for dynamic invocation.

**Parameters:**
- `$name` (string): Method name
- `$arguments` (array): Method arguments

**Returns:** ResponseInterface implementation

### System

**Namespace**: `MotionDots\Method\System`  
**File**: `src/Method/System/System.php`

Built-in system methods for introspection and utilities.

#### Constructor

```php
public function __construct(Schema &$schema_info, string $separator)
```

**Parameters:**
- `$schema_info` (Schema): Schema instance (passed by reference)
- `$separator` (string): Method separator string

#### Methods

##### `getSchema(): array`

Returns the complete API schema.

**Returns:** Array containing schema information

##### `serverTime(): int`

Returns the current server timestamp.

**Returns:** Unix timestamp

##### `increment(int $counter): int`

Increments a counter value.

**Parameters:**
- `$counter` (int): Counter value to increment

**Returns:** Incremented value

## Type System

### AbstractType

**Namespace**: `MotionDots\Type`  
**File**: `src/Type/AbstractType.php`

Base class for all custom parameter types.

#### Properties

##### `$field`

```php
protected $field = null;
```

The raw input value to be validated and parsed.

##### `$param_name`

```php
protected $param_name = null;
```

The parameter name for error reporting.

##### `$context`

```php
protected $context = null;
```

The shared context instance.

#### Constructor

```php
public function __construct($field, ?string $param_name, Context &$context)
```

**Parameters:**
- `$field` (mixed): Raw input value
- `$param_name` (string|null): Parameter name
- `$context` (Context): Context instance (passed by reference)

#### Methods

##### `parse()`

Abstract method that must be implemented by subclasses.

**Returns:** Parsed and validated value

##### `build()`

Returns the field value for serialization.

**Returns:** Field value

### TypeInterface

**Namespace**: `MotionDots\Type`  
**File**: `src/Type/TypeInterface.php`

Interface defining the contract for type classes.

#### Methods

##### `__construct(?string $field, ?string $param_name, Context &$context)`

Constructor signature for type classes.

##### `parse()`

Method for validating and parsing input.

##### `build()`

Method for serializing the type value.

### Built-in Types

#### BuiltinType

**Namespace**: `MotionDots\Type`  
**File**: `src/Type/BuiltinType.php`

Handles PHP built-in types with automatic type conversion.

**Supported Types:** `int`, `float`, `string`, `bool`, `array`

#### EnumType

**Namespace**: `MotionDots\Type`  
**File**: `src/Type/EnumType.php`

Handles PHP 8.1 enums with validation and conversion.

#### CountType

**Namespace**: `MotionDots\Type`  
**File**: `src/Type/CountType.php`

Validates count parameters with range validation (1-1000).

#### PositiveType

**Namespace**: `MotionDots\Type`  
**File**: `src/Type/PositiveType.php`

Ensures positive integer values.

#### IntListType

**Namespace**: `MotionDots\Type`  
**File**: `src/Type/IntListType.php`

Parses comma-separated integer lists.

#### StringListType

**Namespace**: `MotionDots\Type`  
**File**: `src/Type/StringListType.php`

Parses comma-separated string lists.

#### PositiveListType

**Namespace**: `MotionDots\Type`  
**File**: `src/Type/PositiveListType.php`

Parses comma-separated positive integer lists with validation.

## Response System

### AbstractResponse

**Namespace**: `MotionDots\Response`  
**File**: `src/Response/AbstractResponse.php`

Base class for all API response classes.

#### Static Methods

##### `create()`

Factory method to create a new response instance.

**Returns:** New instance of the response class

#### Instance Methods

##### `build(): array`

Builds the response array with automatic serialization.

**Returns:** Array containing the response data

### ResponseInterface

**Namespace**: `MotionDots\Response`  
**File**: `src/Response/ResponseInterface.php`

Interface defining the contract for response classes.

#### Constants

##### `TYPE_ID_FIELD`

```php
public const TYPE_ID_FIELD = '__type_id';
```

Reserved field name for type identification.

#### Methods

##### `create(): ResponseInterface`

Static factory method for creating response instances.

##### `build(): array`

Method for building the response array.

## TypeScript Generation

### Generator

**Namespace**: `MotionDots\Schema\Typescript`  
**File**: `src/Schema/Typescript/Generator.php`

Main generator class for TypeScript file creation.

#### Properties

##### `$excluded_spaces`

```php
private array $excluded_spaces = ['system'];
```

Array of method spaces to exclude from generation.

##### `$is_verbose`

```php
private bool $is_verbose = true;
```

Whether to output verbose generation messages.

##### `$files_path`

```php
private string $files_path = './api-schema';
```

Output directory for generated TypeScript files.

#### Static Methods

##### `create(): self`

Factory method to create a new Generator instance.

**Returns:** New Generator instance

#### Instance Methods

##### `excludeSpaces(string ...$space_names): self`

Adds method spaces to exclude from generation.

**Parameters:**
- `$space_names` (string): Variable number of space names to exclude

**Returns:** Self for method chaining

##### `setIsVerbose(bool $is_verbose): self`

Sets the verbose output mode.

**Parameters:**
- `$is_verbose` (bool): Whether to output verbose messages

**Returns:** Self for method chaining

##### `setFilesPath(string $files_path): self`

Sets the output directory for generated files.

**Parameters:**
- `$files_path` (string): Directory path for output files

**Returns:** Self for method chaining

##### `generate(Processor $processor): void`

Generates TypeScript files from the processor's schema.

**Parameters:**
- `$processor` (Processor): Processor instance with registered methods

### Type Nodes

#### MethodNode

**Namespace**: `MotionDots\Schema\Typescript\Nodes`  
**File**: `src/Schema/Typescript/Nodes/MethodNode.php`

Represents a TypeScript method definition.

#### ObjectNode

**Namespace**: `MotionDots\Schema\Typescript\Nodes`  
**File**: `src/Schema/Typescript/Nodes/ObjectNode.php`

Represents a TypeScript interface for response objects.

#### EnumNode

**Namespace**: `MotionDots\Schema\Typescript\Nodes`  
**File**: `src/Schema/Typescript/Nodes/EnumNode.php`

Represents a TypeScript enum definition.

#### PrimitiveNode

**Namespace**: `MotionDots\Schema\Typescript\Nodes`  
**File**: `src/Schema/Typescript/Nodes/PrimitiveNode.php`

Represents primitive TypeScript types.

### Type Mapper

**Namespace**: `MotionDots\Schema\Typescript`  
**File**: `src/Schema/Typescript/TypeMapper.php`

Maps PHP types to TypeScript types.

#### Static Methods

##### `map(array $schema): TypeNodeInterface`

Creates the appropriate TypeNode based on schema type.

**Parameters:**
- `$schema` (array): Type schema

**Returns:** TypeNodeInterface implementation

##### `mapName(string $type): string`

Maps PHP type names to TypeScript type names.

**Parameters:**
- `$type` (string): PHP type name

**Returns:** TypeScript type name

### Repositories

#### MethodsRepository

**Namespace**: `MotionDots\Schema\Typescript\Repositories`  
**File**: `src/Schema/Typescript/Repositories/MethodsRepository.php`

Manages method definitions for TypeScript generation.

#### ObjectsRepository

**Namespace**: `MotionDots\Schema\Typescript\Repositories`  
**File**: `src/Schema/Typescript/Repositories/ObjectsRepository.php`

Manages object type definitions for TypeScript generation.

#### EnumsRepository

**Namespace**: `MotionDots\Schema\Typescript\Repositories`  
**File**: `src/Schema/Typescript/Repositories/EnumsRepository.php`

Manages enum definitions for TypeScript generation.

## Error Codes

### ErrorException

**Namespace**: `MotionDots\Exception`  
**File**: `src/Exception/ErrorException.php`

Exception class with predefined error codes.

#### Constants

##### `SCHEMA_METHOD_EXISTS`

```php
public const SCHEMA_METHOD_EXISTS = -1;
```

Method already exists in schema.

##### `PARAM_UNSUPPORTED`

```php
public const PARAM_UNSUPPORTED = -2;
```

Unsupported parameter type.

##### `PARAM_UNKNOWN_RESOLVER`

```php
public const PARAM_UNKNOWN_RESOLVER = -3;
```

Unknown parameter resolver.

##### `PARAM_REFLECTION_ERROR`

```php
public const PARAM_REFLECTION_ERROR = -4;
```

Reflection error.

##### `PARAM_IS_REQUIRED`

```php
public const PARAM_IS_REQUIRED = -5;
```

Required parameter missing.

##### `CONTEXT_UNDEFINED_FIELD`

```php
public const CONTEXT_UNDEFINED_FIELD = -6;
```

Context field not found.

##### `METHOD_ACTION_UNDEFINED`

```php
public const METHOD_ACTION_UNDEFINED = -7;
```

Action not found in method.

##### `METHOD_UNDEFINED`

```php
public const METHOD_UNDEFINED = -8;
```

Method not registered.

##### `PARAM_INCORRECT`

```php
public const PARAM_INCORRECT = -9;
```

Parameter validation failed.

##### `INTERNAL_ERROR`

```php
public const INTERNAL_ERROR = -10;
```

Internal system error.

#### Constructor

```php
public function __construct(int $error_code = null, ?string $additional_message = null)
```

**Parameters:**
- `$error_code` (int): Error code
- `$additional_message` (string|null): Additional error message

## Constants

### ResponseInterface

**Namespace**: `MotionDots\Response`

#### `TYPE_ID_FIELD`

```php
public const TYPE_ID_FIELD = '__type_id';
```

Reserved field name for type identification in responses.

### TypeScript Generation

#### MethodNode

**Namespace**: `MotionDots\Schema\Typescript\Nodes`

##### `TYPE`

```php
public const TYPE = 'method';
```

Type identifier for method nodes.

#### ObjectNode

**Namespace**: `MotionDots\Schema\Typescript\Nodes`

##### `TYPE`

```php
public const TYPE = 'object';
```

Type identifier for object nodes.

#### EnumNode

**Namespace**: `MotionDots\Schema\Typescript\Nodes`

##### `TYPE`

```php
public const TYPE = 'enum';
```

Type identifier for enum nodes.

## Usage Patterns

### Basic Method Definition

```php
class Users extends AbstractMethod {
    public function getUser(PositiveType $id): UserResponse {
        $userId = $id->parse();
        // Method implementation
        return UserResponse::create()->setId($userId);
    }
}
```

### Custom Type Definition

```php
class EmailType extends AbstractType {
    public function parse(): string {
        $email = filter_var($this->field, FILTER_VALIDATE_EMAIL);
        if ($email === false) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                "`{$this->param_name}` must be a valid email address"
            );
        }
        return $email;
    }
}
```

### Response Definition

```php
class UserResponse extends AbstractResponse {
    public int $id = 0;
    public string $name = '';
    
    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }
    
    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }
}
```

### TypeScript Generation

```php
Generator::create()
    ->setFilesPath('./frontend/src/types/api')
    ->setIsVerbose(true)
    ->excludeSpaces('admin', 'debug')
    ->generate($processor);
```

This API reference provides comprehensive documentation for all classes, methods, and constants in the MotionDots framework, enabling developers to effectively use and extend the system.
