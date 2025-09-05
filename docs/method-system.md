# Method System Documentation

## Overview

The Method System in MotionDots provides the foundation for defining API endpoints. It consists of abstract base classes, interfaces, and built-in system methods that enable dynamic method invocation and introspection.

## AbstractMethod Class

**File**: `src/Method/AbstractMethod.php`  
**Namespace**: `MotionDots\Method`

The `AbstractMethod` class is the base class for all API method classes. It provides context injection, magic method handling, and method introspection capabilities.

### Properties

#### `$context`

```php
public $context;
```

The shared context instance that provides access to request data across methods and types.

### Methods

#### `__setContext(Context &$context)`

Sets the context instance for the method.

**Parameters:**
- `$context` (Context): Context instance (passed by reference)

**Usage:**
```php
// Called automatically by the framework
$method->__setContext($context);
```

#### `__call($name, $arguments): ResponseInterface`

Magic method that handles dynamic method invocation.

**Parameters:**
- `$name` (string): Method name to call
- `$arguments` (array): Method arguments

**Returns:** ResponseInterface implementation

**Usage:**
```php
// Called automatically when invoking methods
$result = $method->getUser($id, $name);
```

#### `__toString(): string`

Returns the method name for schema registration.

**Returns:** Method name (class name in camelCase)

**Example:**
```php
class Users extends AbstractMethod {}
echo (string)new Users(); // Returns: "users"
```

#### `__actionExists(string $action): bool`

Checks if an action (public method) exists in the method class.

**Parameters:**
- `$action` (string): Action name to check

**Returns:** True if action exists, false otherwise

**Example:**
```php
$users = new Users();
if ($users->__actionExists('getUser')) {
    // Method has getUser action
}
```

### Creating Method Classes

To create a new method class, extend `AbstractMethod` and define public methods:

```php
<?php
namespace API\Methods;

use MotionDots\Method\AbstractMethod;
use API\Responses\UserResponse;
use MotionDots\Type\PositiveType;

class Users extends AbstractMethod {
    
    public function getUser(PositiveType $id): UserResponse {
        $userId = $id->parse();
        
        // Access context
        $requestTime = $this->context->get('requestTime');
        
        // Business logic here
        $user = $this->findUser($userId);
        
        return UserResponse::create()
            ->setId($userId)
            ->setName($user['name']);
    }
    
    public function createUser(string $name, string $email): UserResponse {
        // Create user logic
        $userId = $this->saveUser($name, $email);
        
        // Set data in context
        $this->context->set('createdUserId', $userId);
        
        return UserResponse::create()
            ->setId($userId)
            ->setName($name);
    }
    
    private function findUser(int $id): array {
        // Private methods are not exposed as API endpoints
        return ['name' => 'John Doe'];
    }
}
```

### Method Naming Convention

- **Class Name**: PascalCase (e.g., `Users`, `UserManagement`)
- **Method Name**: camelCase (e.g., `getUser`, `createUser`, `updateUserStatus`)
- **API Endpoint**: `{className}.{methodName}` (e.g., `users.getUser`)

### Method Visibility

- **Public Methods**: Exposed as API endpoints
- **Private Methods**: Not exposed, used for internal logic
- **Protected Methods**: Not exposed, used for inheritance

## MethodInterface

**File**: `src/Method/MethodInterface.php`  
**Namespace**: `MotionDots\Method`

The `MethodInterface` defines the contract that all method classes must implement.

### Methods

#### `__call($name, $arguments): ResponseInterface`

Defines the magic method signature for dynamic invocation.

**Parameters:**
- `$name` (string): Method name
- `$arguments` (array): Method arguments

**Returns:** ResponseInterface implementation

## System Methods

**File**: `src/Method/System/System.php`  
**Namespace**: `MotionDots\Method\System`

The System class provides built-in methods for schema introspection and utility functions.

### Constructor

```php
public function __construct(Schema &$schema_info, string $separator)
```

**Parameters:**
- `$schema_info` (Schema): Schema instance (passed by reference)
- `$separator` (string): Method separator string

### Methods

#### `getSchema(): array`

Returns the complete API schema including all methods, parameters, and response types.

**Returns:** Array containing schema information

**Response Structure:**
```php
[
    [
        'name' => 'users',
        'methods' => [
            [
                'name' => 'users.getUser',
                'params' => [
                    [
                        'name' => 'id',
                        'type' => 'int',
                        'required' => true
                    ]
                ],
                'response' => [
                    'type' => 'object',
                    'type_name' => 'UserResponse',
                    'properties' => [...]
                ]
            ]
        ]
    ]
]
```

#### `serverTime(): int`

Returns the current server timestamp.

**Returns:** Unix timestamp

**Example:**
```php
$timestamp = $system->serverTime();
// Returns: 1640995200
```

#### `increment(int $counter): int`

Increments a counter value.

**Parameters:**
- `$counter` (int): Counter value to increment

**Returns:** Incremented value

**Example:**
```php
$newValue = $system->increment(5);
// Returns: 6
```

### System Method Responses

#### SystemMethodResponse

**File**: `src/Method/System/Response/SystemMethodResponse.php`

Response structure for individual method definitions.

**Properties:**
- `$name` (string): Method name
- `$params` (array|null): Method parameters
- `$response` (array): Response structure

#### SystemMethodsResponse

**File**: `src/Method/System/Response/SystemMethodsResponse.php`

Response structure for method collections.

**Properties:**
- `$name` (string|null): Method group name
- `$methods` (array|null): Array of method definitions

#### SystemMethodParamResponse

**File**: `src/Method/System/Response/SystemMethodParamResponse.php`

Response structure for parameter definitions.

**Properties:**
- `$name` (string|null): Parameter name
- `$type` (string): Parameter type
- `$required` (bool): Whether parameter is required
- `$items` (mixed): Array item type (for array parameters)
- `$properties` (array|null): Object properties (for object parameters)
- `$enum` (array|null): Enum values
- `$enum_names` (array|null): Enum names
- `$type_name` (string|null): Type name for complex types

## Method Registration

Methods are registered with the Schema class:

```php
<?php
use MotionDots\Schema\Schema;
use API\Methods\Users;
use API\Methods\Products;

// Create schema
$schema = Schema::create();

// Add single method
$schema->addMethod(new Users());

// Add multiple methods
$schema->addMethods([
    new Users(),
    new Products(),
    new Orders()
]);
```

## Method Invocation

Methods are invoked through the Processor:

```php
<?php
use MotionDots\Process\Processor;

$processor = new Processor($schema, '.');

// Invoke method
$response = $processor->invokeProcess('users.getUser', [
    'id' => 123
]);

// With error handling
$result = $processor->execute('users.getUser', [
    'id' => 123
]);
```

## Context Usage in Methods

Methods have access to the shared context:

```php
class Users extends AbstractMethod {
    
    public function getUser(PositiveType $id): UserResponse {
        // Get data from context
        $requestTime = $this->context->get('requestTime');
        $clientIp = $this->context->get('clientIp');
        
        // Set data in context
        $this->context->set('lastAccessedUser', $id->parse());
        
        // Use context data
        $user = $this->findUser($id->parse(), $clientIp);
        
        return UserResponse::create()
            ->setId($id->parse())
            ->setName($user['name']);
    }
}
```

## Error Handling in Methods

Methods can throw `ErrorException` for various error conditions:

```php
use MotionDots\Exception\ErrorException;

class Users extends AbstractMethod {
    
    public function getUser(PositiveType $id): UserResponse {
        $userId = $id->parse();
        
        if ($userId === 0) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                'User ID cannot be zero'
            );
        }
        
        $user = $this->findUser($userId);
        if (!$user) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                'User not found'
            );
        }
        
        return UserResponse::create()
            ->setId($userId)
            ->setName($user['name']);
    }
}
```

## Best Practices

### Method Design

1. **Single Responsibility**: Each method should have a single, clear purpose
2. **Type Safety**: Use typed parameters and return types
3. **Error Handling**: Throw appropriate exceptions with meaningful messages
4. **Context Usage**: Use context for shared data, not for method parameters
5. **Private Methods**: Use private methods for internal logic

### Naming Conventions

1. **Class Names**: Use PascalCase and descriptive names
2. **Method Names**: Use camelCase and action verbs
3. **API Endpoints**: Follow `{class}.{method}` pattern

### Parameter Design

1. **Required Parameters**: Use typed parameters for required data
2. **Optional Parameters**: Use nullable types for optional data
3. **Validation**: Use custom types for complex validation
4. **Default Values**: Avoid default values in method signatures

### Response Design

1. **Consistent Structure**: Use response classes for consistent structure
2. **Type Safety**: Use typed response properties
3. **Null Handling**: Handle null values appropriately
4. **Error Responses**: Let the framework handle error responses

## Example: Complete Method Class

```php
<?php
namespace API\Methods;

use MotionDots\Method\AbstractMethod;
use MotionDots\Exception\ErrorException;
use API\Responses\UserResponse;
use API\Responses\ListResponse;
use MotionDots\Type\PositiveType;
use MotionDots\Type\PositiveListType;

class Users extends AbstractMethod {
    
    /**
     * Get a single user by ID
     */
    public function getUser(PositiveType $id): UserResponse {
        $userId = $id->parse();
        
        // Access context
        $requestTime = $this->context->get('requestTime');
        
        // Business logic
        $user = $this->findUser($userId);
        if (!$user) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                "User with ID {$userId} not found"
            );
        }
        
        // Set context data
        $this->context->set('lastAccessedUser', $userId);
        
        return UserResponse::create()
            ->setId($userId)
            ->setName($user['name'])
            ->setEmail($user['email']);
    }
    
    /**
     * Get multiple users by IDs
     */
    public function getMany(PositiveListType $ids): ListResponse {
        $userIds = $ids->parse();
        $users = [];
        
        foreach ($userIds as $userId) {
            $user = $this->findUser($userId);
            if ($user) {
                $users[] = UserResponse::create()
                    ->setId($userId)
                    ->setName($user['name'])
                    ->setEmail($user['email']);
            }
        }
        
        return ListResponse::create()
            ->setItems($users)
            ->setCount(count($users));
    }
    
    /**
     * Create a new user
     */
    public function createUser(string $name, string $email): UserResponse {
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                'Invalid email format'
            );
        }
        
        // Create user
        $userId = $this->saveUser($name, $email);
        
        // Set context data
        $this->context->set('createdUserId', $userId);
        
        return UserResponse::create()
            ->setId($userId)
            ->setName($name)
            ->setEmail($email);
    }
    
    /**
     * Update user status (optional parameters)
     */
    public function updateUserStatus(
        PositiveType $id, 
        ?string $status = null
    ): UserResponse {
        $userId = $id->parse();
        
        // Get current user
        $user = $this->findUser($userId);
        if (!$user) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                "User with ID {$userId} not found"
            );
        }
        
        // Update status if provided
        if ($status !== null) {
            $this->updateUserStatus($userId, $status);
            $user['status'] = $status;
        }
        
        return UserResponse::create()
            ->setId($userId)
            ->setName($user['name'])
            ->setEmail($user['email']);
    }
    
    /**
     * Private method for internal logic
     */
    private function findUser(int $id): ?array {
        // Database query logic
        return ['name' => 'John Doe', 'email' => 'john@example.com'];
    }
    
    private function saveUser(string $name, string $email): int {
        // Database insert logic
        return 123;
    }
    
    private function updateUserStatus(int $id, string $status): void {
        // Database update logic
    }
}
```
