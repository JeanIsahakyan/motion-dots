# Core Classes Documentation

## Processor Class

**File**: `src/Process/Processor.php`  
**Namespace**: `MotionDots\Process`

The Processor is the main orchestrator of the MotionDots framework, handling the entire request lifecycle from method invocation to response building.

### Constructor

```php
public function __construct(Schema $schema, string $separator = '.', bool $disable_system_methods = false)
```

**Parameters:**
- `$schema` (Schema): The schema instance containing registered methods
- `$separator` (string): Separator used for method.action naming (default: '.')
- `$disable_system_methods` (bool): Whether to disable built-in system methods (default: false)

**Example:**
```php
$schema = Schema::create()->addMethods([new Users()]);
$processor = new Processor($schema, '.');
```

### Methods

#### `invokeProcess(string $method, array &$params = []): array`

Invokes a method and returns the structured response.

**Parameters:**
- `$method` (string): Method name in format "methodName.actionName"
- `$params` (array): Request parameters (passed by reference)

**Returns:** Array containing the response data

**Example:**
```php
$response = $processor->invokeProcess('users.getUser', ['id' => 123]);
// Returns: ['response' => ['id' => 123, 'name' => 'John Doe']]
```

#### `execute(string $method, array &$params = []): array`

Executes a method with error handling, returning either success response or error response.

**Parameters:**
- `$method` (string): Method name in format "methodName.actionName"
- `$params` (array): Request parameters (passed by reference)

**Returns:** Array containing either response data or error information

**Example:**
```php
$result = $processor->execute('users.getUser', ['id' => 123]);
// Success: ['response' => [...]]
// Error: ['error' => ['error_code' => -9, 'error_message' => '...']]
```

#### `getContext(): Context`

Returns the shared context instance for the current request.

**Returns:** Context instance

**Example:**
```php
$context = $processor->getContext();
$context->set('userId', 123);
```

#### `getSeparator(): string`

Returns the method separator string.

**Returns:** String separator (default: '.')

## Schema Class

**File**: `src/Schema/Schema.php`  
**Namespace**: `MotionDots\Schema`

The Schema class manages method registration and provides method lookup and invocation capabilities.

### Static Methods

#### `create(): self`

Creates a new Schema instance.

**Returns:** New Schema instance

**Example:**
```php
$schema = Schema::create();
```

### Instance Methods

#### `addMethod(AbstractMethod $method): self`

Adds a single method to the schema.

**Parameters:**
- `$method` (AbstractMethod): Method instance to register

**Returns:** Self for method chaining

**Throws:** `ErrorException` if method already exists

**Example:**
```php
$schema->addMethod(new Users());
```

#### `addMethods(array $methods): self`

Adds multiple methods to the schema.

**Parameters:**
- `$methods` (array): Array of AbstractMethod instances

**Returns:** Self for method chaining

**Example:**
```php
$schema->addMethods([
    new Users(),
    new Products(),
    new Orders()
]);
```

#### `methodExists(string $method): bool`

Checks if a method is registered.

**Parameters:**
- `$method` (string): Method name to check

**Returns:** True if method exists, false otherwise

**Example:**
```php
if ($schema->methodExists('users')) {
    // Method is registered
}
```

#### `getMethod(string $method): AbstractMethod`

Retrieves a registered method.

**Parameters:**
- `$method` (string): Method name

**Returns:** AbstractMethod instance

**Example:**
```php
$usersMethod = $schema->getMethod('users');
```

#### `getMethods(): array`

Returns all registered methods.

**Returns:** Array of AbstractMethod instances

**Example:**
```php
$allMethods = $schema->getMethods();
foreach ($allMethods as $name => $method) {
    echo "Method: $name\n";
}
```

#### `tryInvokeProcess(Context &$context, string $method_name, string &$action, array &$params)`

Internal method for invoking processes with context.

**Parameters:**
- `$context` (Context): Request context (passed by reference)
- `$method_name` (string): Method name
- `$action` (string): Action name (passed by reference)
- `$params` (array): Request parameters (passed by reference)

**Returns:** Method execution result

**Throws:** `ErrorException` for various error conditions

## Context Class

**File**: `src/Process/Context.php`  
**Namespace**: `MotionDots\Process`

The Context class provides a shared data container accessible across methods and types during the request lifecycle.

### Constructor

```php
public function __construct()
```

Creates a new context instance with an empty data store.

### Methods

#### `get(string $field)`

Retrieves a value from the context.

**Parameters:**
- `$field` (string): Field name to retrieve

**Returns:** The stored value

**Throws:** `ErrorException` if field doesn't exist

**Example:**
```php
$userId = $context->get('userId');
```

#### `set(string $field, $value): self`

Sets a value in the context.

**Parameters:**
- `$field` (string): Field name
- `$value` (mixed): Value to store

**Returns:** Self for method chaining

**Example:**
```php
$context->set('userId', 123);
$context->set('userRole', 'admin');
```

#### `setMany(array $fields): void`

Sets multiple values in the context.

**Parameters:**
- `$fields` (array): Associative array of field => value pairs

**Example:**
```php
$context->setMany([
    'userId' => 123,
    'userRole' => 'admin',
    'requestTime' => microtime(true)
]);
```

#### `getAll(): array`

Returns all context data.

**Returns:** Array of all stored data

**Example:**
```php
$allData = $context->getAll();
// Returns: ['userId' => 123, 'userRole' => 'admin', ...]
```

## MethodProcessor Class

**File**: `src/Process/MethodProcessor.php`  
**Namespace**: `MotionDots\Process`

The MethodProcessor handles individual method execution with parameter parsing and validation.

### Constructor

```php
public function __construct(AbstractMethod &$method, string &$action, array &$params = [])
```

**Parameters:**
- `$method` (AbstractMethod): Method instance (passed by reference)
- `$action` (string): Action name (passed by reference)
- `$params` (array): Request parameters (passed by reference)

**Throws:** `ErrorException` for reflection errors

### Static Methods

#### `create(AbstractMethod &$method, string &$action, array $params = []): self`

Factory method to create a MethodProcessor instance.

**Parameters:**
- `$method` (AbstractMethod): Method instance (passed by reference)
- `$action` (string): Action name (passed by reference)
- `$params` (array): Request parameters

**Returns:** New MethodProcessor instance

**Throws:** `ErrorException` for reflection errors

**Example:**
```php
$processor = MethodProcessor::create($method, $action, $params);
```

### Instance Methods

#### `invoke()`

Invokes the target method with parsed parameters.

**Returns:** Method execution result

**Example:**
```php
$result = $methodProcessor->invoke();
```

## ParamParser Class

**File**: `src/Process/ParamParser.php`  
**Namespace**: `MotionDots\Process`

The ParamParser validates and parses method parameters using the type system.

### Constructor

```php
public function __construct(\ReflectionMethod &$reflection, Context &$context, &$params)
```

**Parameters:**
- `$reflection` (ReflectionMethod): Method reflection (passed by reference)
- `$context` (Context): Request context (passed by reference)
- `$params` (array): Request parameters (passed by reference)

### Methods

#### `getParams()`

Returns the processed parameters ready for method invocation.

**Returns:** Array of processed parameters

**Example:**
```php
$processedParams = $paramParser->getParams();
// Returns: [TypeInstance1, TypeInstance2, ...]
```

## ResponseBuilder Class

**File**: `src/Process/ResponseBuilder.php`  
**Namespace**: `MotionDots\Process`

The ResponseBuilder converts method responses to structured JSON format.

### Static Methods

#### `build($response): array`

Builds a structured response from method output.

**Parameters:**
- `$response` (mixed): Method response

**Returns:** Structured response array

**Example:**
```php
$structuredResponse = ResponseBuilder::build($methodResult);
// Returns: ['response' => $processedData]
```

#### `tryBuild($response)`

Recursively processes response data for JSON serialization.

**Parameters:**
- `$response` (mixed): Response data to process

**Returns:** Processed response data

**Features:**
- Handles scalar values
- Processes BackedEnum instances
- Recursively processes objects and arrays
- Calls `build()` method on objects that have it

## Error Handling

All core classes use the `ErrorException` class for error handling with predefined error codes:

- `SCHEMA_METHOD_EXISTS` (-1): Method already registered
- `PARAM_UNSUPPORTED` (-2): Unsupported parameter type
- `PARAM_UNKNOWN_RESOLVER` (-3): Unknown parameter resolver
- `PARAM_REFLECTION_ERROR` (-4): Reflection error
- `PARAM_IS_REQUIRED` (-5): Required parameter missing
- `CONTEXT_UNDEFINED_FIELD` (-6): Context field not found
- `METHOD_ACTION_UNDEFINED` (-7): Action not found in method
- `METHOD_UNDEFINED` (-8): Method not registered
- `PARAM_INCORRECT` (-9): Parameter validation failed
- `INTERNAL_ERROR` (-10): Internal system error

## Usage Examples

### Basic Setup

```php
<?php
use MotionDots\Process\Processor;
use MotionDots\Schema\Schema;
use API\Methods\Users;

// Create schema
$schema = Schema::create()->addMethods([
    new Users(),
]);

// Create processor
$processor = new Processor($schema, '.');

// Set initial context
$processor->getContext()->setMany([
    'requestTime' => microtime(true),
    'clientIp' => $_SERVER['REMOTE_ADDR']
]);

// Handle request
$response = $processor->invokeProcess('users.getUser', ['id' => 123]);
echo json_encode($response);
```

### Error Handling

```php
try {
    $response = $processor->invokeProcess('users.getUser', ['id' => 123]);
    echo json_encode($response);
} catch (\Exception $exception) {
    echo json_encode([
        'error' => [
            'error_code' => $exception->getCode(),
            'error_message' => $exception->getMessage(),
        ]
    ]);
}
```

### Using Context

```php
// In a method class
public function getUser(PositiveType $id): UserResponse {
    $userId = $id->parse();
    
    // Access context
    $requestTime = $this->context->get('requestTime');
    
    // Set data in context
    $this->context->set('lastAccessedUser', $userId);
    
    return UserResponse::create()->setId($userId);
}
```
