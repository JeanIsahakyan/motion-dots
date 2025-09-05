# MotionDots Architecture

## Overview

MotionDots follows a clean, modular architecture designed for API development. The framework is built around several core components that work together to provide a robust and type-safe API development experience.

## Core Components

### 1. Process Layer

The process layer is the heart of MotionDots, handling request processing and method invocation.

#### Processor (`src/Process/Processor.php`)

The main orchestrator that coordinates the entire request lifecycle:

```php
class Processor {
    protected Context $context;
    protected Schema $schema;
    protected string $separator;
    
    public function __construct(Schema $schema, string $separator = '.', bool $disable_system_methods = false)
    public function invokeProcess(string $method, array &$params = []): array
    public function execute(string $method, array &$params = []): array
    public function getContext(): Context
}
```

**Key Responsibilities:**
- Manages the request context
- Coordinates method invocation
- Handles error responses
- Integrates with the schema system

#### MethodProcessor (`src/Process/MethodProcessor.php`)

Handles individual method execution with parameter parsing:

```php
class MethodProcessor {
    protected AbstractMethod $method;
    protected string $action;
    protected \ReflectionMethod $reflection;
    protected ParamParser $params;
    
    public function __construct(AbstractMethod &$method, string &$action, array &$params = [])
    public function invoke()
}
```

**Key Responsibilities:**
- Creates reflection for method introspection
- Parses and validates parameters
- Invokes the target method
- Handles method-specific errors

#### ParamParser (`src/Process/ParamParser.php`)

Validates and parses method parameters using the type system:

```php
class ParamParser {
    protected array $params;
    protected array $processed_params;
    protected \ReflectionMethod $reflection;
    protected Context $context;
    
    public function __construct(\ReflectionMethod &$reflection, Context &$context, &$params)
    public function getParams()
}
```

**Key Responsibilities:**
- Validates required parameters
- Instantiates appropriate type handlers
- Handles built-in types, enums, and custom types
- Provides parsed parameters to methods

#### Context (`src/Process/Context.php`)

Shared data container accessible across methods and types:

```php
class Context {
    private \ArrayObject $fields;
    
    public function get(string $field)
    public function set(string $field, $value): self
    public function setMany(array $fields): void
    public function getAll(): array
}
```

**Key Responsibilities:**
- Stores shared data during request lifecycle
- Provides access to context from methods and types
- Manages data persistence across method calls

### 2. Schema Layer

The schema layer manages method registration and provides introspection capabilities.

#### Schema (`src/Schema/Schema.php`)

Central registry for all API methods:

```php
class Schema {
    protected array $methods = [];
    
    public static function create(): self
    public function addMethod(AbstractMethod $method): self
    public function addMethods(array $methods): self
    public function methodExists(string $method): bool
    public function getMethod(string $method): AbstractMethod
    public function tryInvokeProcess(Context &$context, string $method_name, string &$action, array &$params)
}
```

**Key Responsibilities:**
- Registers and manages method classes
- Provides method lookup and invocation
- Handles method existence validation
- Coordinates with the process layer

### 3. Method System

The method system provides the foundation for API endpoint definition.

#### AbstractMethod (`src/Method/AbstractMethod.php`)

Base class for all API methods:

```php
abstract class AbstractMethod implements MethodInterface {
    public $context;
    
    public function __setContext(Context &$context)
    public function __call($name, $arguments): ResponseInterface
    public function __toString(): string
    public function __actionExists(string $action): bool
}
```

**Key Features:**
- Context injection for shared data access
- Magic method handling for dynamic invocation
- Method name resolution
- Action existence validation

#### System Methods (`src/Method/System/`)

Built-in system methods for introspection and utilities:

- **System.php**: Main system class with schema introspection
- **SystemMethodResponse.php**: Response structure for method definitions
- **SystemMethodsResponse.php**: Response structure for method collections
- **SystemMethodParamResponse.php**: Response structure for parameter definitions

### 4. Type System

The type system provides validation and parsing for method parameters.

#### AbstractType (`src/Type/AbstractType.php`)

Base class for all custom parameter types:

```php
abstract class AbstractType implements TypeInterface {
    protected $field;
    protected $param_name;
    protected $context;
    
    public function __construct($field, ?string $param_name, Context &$context)
    public function parse()
    public function build()
}
```

#### Built-in Types

- **BuiltinType**: Handles PHP built-in types (int, string, bool, etc.)
- **EnumType**: Supports PHP 8.1 enums
- **CountType**: Validates count parameters (1-1000)
- **PositiveType**: Ensures positive integer values
- **IntListType**: Parses comma-separated integer lists
- **StringListType**: Parses comma-separated string lists
- **PositiveListType**: Parses comma-separated positive integer lists

### 5. Response System

The response system provides structured data formatting for API responses.

#### AbstractResponse (`src/Response/AbstractResponse.php`)

Base class for all API responses:

```php
abstract class AbstractResponse implements ResponseInterface {
    public static function create()
    public function build(): array
}
```

**Key Features:**
- Automatic JSON serialization
- Type identification for frontend consumption
- Null value filtering
- Fluent interface support

### 6. TypeScript Generation

The TypeScript generation system creates type definitions for frontend integration.

#### Generator (`src/Schema/Typescript/Generator.php`)

Main generator class that orchestrates TypeScript file creation:

```php
class Generator {
    private array $excluded_spaces;
    private bool $is_verbose;
    private string $files_path;
    
    public static function create(): self
    public function excludeSpaces(string ...$space_names): self
    public function setIsVerbose(bool $is_verbose): self
    public function setFilesPath(string $files_path): self
    public function generate(Processor $processor): void
}
```

#### Type Nodes

- **MethodNode**: Generates method definitions and interfaces
- **ObjectNode**: Generates TypeScript interfaces for response objects
- **EnumNode**: Generates TypeScript enums
- **PrimitiveNode**: Handles primitive type mapping

#### Repositories

- **MethodsRepository**: Manages method definitions
- **ObjectsRepository**: Manages object type definitions
- **EnumsRepository**: Manages enum definitions

## Data Flow

```
Request → Processor → Schema → MethodProcessor → ParamParser → Method → ResponseBuilder → Response
```

1. **Request arrives** at the Processor
2. **Processor** extracts method name and parameters
3. **Schema** looks up the method class
4. **MethodProcessor** creates reflection and parses parameters
5. **ParamParser** validates and converts parameters using type system
6. **Method** executes with parsed parameters
7. **ResponseBuilder** formats the response
8. **Response** is returned as JSON

## Error Handling

MotionDots uses a comprehensive error system with predefined error codes:

- **Schema Errors**: Method registration and lookup issues
- **Parameter Errors**: Validation and parsing failures
- **Method Errors**: Invocation and reflection issues
- **Context Errors**: Shared data access problems
- **Internal Errors**: System-level failures

## Extension Points

The architecture provides several extension points:

1. **Custom Methods**: Extend AbstractMethod for new API endpoints
2. **Custom Types**: Extend AbstractType for new parameter types
3. **Custom Responses**: Extend AbstractResponse for new response formats
4. **TypeScript Nodes**: Create new node types for custom TypeScript generation
5. **System Methods**: Add new built-in methods for common functionality

This modular architecture ensures that MotionDots is both powerful and extensible, allowing developers to build robust APIs while maintaining clean, maintainable code.
