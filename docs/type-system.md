# Type System Documentation

## Overview

The Type System in MotionDots provides a robust framework for parameter validation, parsing, and type safety. It supports built-in PHP types, custom types, and PHP 8.1 enums, ensuring that API parameters are properly validated and converted before being passed to methods.

## AbstractType Class

**File**: `src/Type/AbstractType.php`  
**Namespace**: `MotionDots\Type`

The `AbstractType` class is the base class for all custom parameter types. It provides the foundation for type validation and parsing.

### Properties

#### `$field`

```php
protected $field = null;
```

The raw input value to be validated and parsed.

#### `$param_name`

```php
protected $param_name = null;
```

The parameter name for error reporting.

#### `$context`

```php
protected $context = null;
```

The shared context instance for accessing request data.

### Constructor

```php
public function __construct($field, ?string $param_name, Context &$context)
```

**Parameters:**
- `$field` (mixed): Raw input value
- `$param_name` (string|null): Parameter name
- `$context` (Context): Context instance (passed by reference)

### Methods

#### `parse()`

Abstract method that must be implemented by subclasses to validate and parse the input.

**Returns:** Parsed and validated value

**Example:**
```php
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
```

#### `build()`

Returns the field value for serialization.

**Returns:** Field value

**Default Implementation:**
```php
public function build() {
    return $this->field;
}
```

## TypeInterface

**File**: `src/Type/TypeInterface.php`  
**Namespace**: `MotionDots\Type`

The `TypeInterface` defines the contract that all type classes must implement.

### Methods

#### `__construct(?string $field, ?string $param_name, Context &$context)`

Constructor signature for type classes.

#### `parse()`

Method for validating and parsing input.

#### `build()`

Method for serializing the type value.

## Built-in Types

### BuiltinType

**File**: `src/Type/BuiltinType.php`  
**Namespace**: `MotionDots\Type`

Handles PHP built-in types with automatic type conversion.

#### Constructor

```php
public function __construct($field, ?string $param_name, Context &$context)
```

**Parameters:**
- `$field` (array): Array containing `[type, value]`
- `$param_name` (string|null): Parameter name
- `$context` (Context): Context instance

#### Supported Types

- `int` / `integer`
- `float` / `double`
- `string`
- `bool` / `boolean`
- `array`

#### Type Conversion

The `BuiltinType` automatically converts string inputs to the appropriate PHP type:

```php
// String "123" becomes integer 123
$intType = new BuiltinType(['int', '123'], 'id', $context);
$value = $intType->parse(); // Returns: 123

// String "true" becomes boolean true
$boolType = new BuiltinType(['bool', 'true'], 'active', $context);
$value = $boolType->parse(); // Returns: true
```

### EnumType

**File**: `src/Type/EnumType.php`  
**Namespace**: `MotionDots\Type`

Handles PHP 8.1 enums with validation and conversion.

#### Constructor

```php
public function __construct($field, ?string $param_name, Context &$context)
```

**Parameters:**
- `$field` (array): Array containing `[enum_class, value]`
- `$param_name` (string|null): Parameter name
- `$context` (Context): Context instance

#### Usage Example

```php
enum UserStatus: string {
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case BANNED = 'banned';
}

// In method parameter
public function updateUserStatus(UserStatus $status): UserResponse {
    $statusValue = $status->value; // 'active', 'inactive', or 'banned'
    // Method logic
}
```

## Specialized Types

### CountType

**File**: `src/Type/CountType.php`  
**Namespace**: `MotionDots\Type`

Validates count parameters with range validation (1-1000).

#### Usage

```php
public function getUsers(CountType $count): ListResponse {
    $limit = $count->parse(); // Validated integer between 1-1000
    // Method logic
}
```

#### Validation Rules

- Must be a positive integer
- Must be between 1 and 1000 (inclusive)
- Throws `ErrorException::PARAM_INCORRECT` if validation fails

### PositiveType

**File**: `src/Type/PositiveType.php`  
**Namespace**: `MotionDots\Type`

Ensures positive integer values.

#### Usage

```php
public function getUser(PositiveType $id): UserResponse {
    $userId = $id->parse(); // Validated positive integer
    // Method logic
}
```

#### Validation Rules

- Must be a non-negative integer
- Throws `ErrorException::PARAM_INCORRECT` if validation fails

### IntListType

**File**: `src/Type/IntListType.php`  
**Namespace**: `MotionDots\Type`

Parses comma-separated integer lists.

#### Usage

```php
public function getUsers(IntListType $ids): ListResponse {
    $userIds = $ids->parse(); // Array of integers
    // Method logic
}
```

#### Input Format

```
"1,2,3,4,5" → [1, 2, 3, 4, 5]
"10,20,30"  → [10, 20, 30]
```

### StringListType

**File**: `src/Type/StringListType.php`  
**Namespace**: `MotionDots\Type`

Parses comma-separated string lists.

#### Usage

```php
public function searchUsers(StringListType $names): ListResponse {
    $searchNames = $names->parse(); // Array of strings
    // Method logic
}
```

#### Input Format

```
"john,jane,bob" → ["john", "jane", "bob"]
"admin,user"    → ["admin", "user"]
```

### PositiveListType

**File**: `src/Type/PositiveListType.php`  
**Namespace**: `MotionDots\Type`

Parses comma-separated positive integer lists with validation.

#### Usage

```php
public function getUsers(PositiveListType $ids): ListResponse {
    $userIds = $ids->parse(); // Array of positive integers
    // Method logic
}
```

#### Validation Rules

- Filters out non-positive values
- Throws `ErrorException::PARAM_INCORRECT` if no valid values remain

## Creating Custom Types

### Basic Custom Type

```php
<?php
namespace API\Types;

use MotionDots\Type\AbstractType;
use MotionDots\Exception\ErrorException;

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

### Advanced Custom Type with Context

```php
<?php
namespace API\Types;

use MotionDots\Type\AbstractType;
use MotionDots\Exception\ErrorException;

class UserIdType extends AbstractType {
    
    public function parse(): int {
        $userId = (int)$this->field;
        
        if ($userId <= 0) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                "`{$this->param_name}` must be a positive integer"
            );
        }
        
        // Access context for additional validation
        $currentUserId = $this->context->get('currentUserId');
        
        // Check if user can access this user ID
        if ($currentUserId !== $userId && !$this->isAdmin()) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                "Access denied to user ID {$userId}"
            );
        }
        
        return $userId;
    }
    
    private function isAdmin(): bool {
        try {
            $userRole = $this->context->get('userRole');
            return $userRole === 'admin';
        } catch (\Exception $e) {
            return false;
        }
    }
}
```

### Complex Validation Type

```php
<?php
namespace API\Types;

use MotionDots\Type\AbstractType;
use MotionDots\Exception\ErrorException;

class PasswordType extends AbstractType {
    
    public function parse(): string {
        $password = (string)$this->field;
        
        if (strlen($password) < 8) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                "`{$this->param_name}` must be at least 8 characters long"
            );
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                "`{$this->param_name}` must contain at least one uppercase letter"
            );
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                "`{$this->param_name}` must contain at least one lowercase letter"
            );
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                "`{$this->param_name}` must contain at least one number"
            );
        }
        
        return $password;
    }
}
```

### Array Type with Item Validation

```php
<?php
namespace API\Types;

use MotionDots\Type\AbstractType;
use MotionDots\Exception\ErrorException;

class IdListType extends AbstractType {
    
    public function parse(): array {
        $input = (string)$this->field;
        $ids = explode(',', $input);
        $validIds = [];
        
        foreach ($ids as $id) {
            $id = trim($id);
            if (is_numeric($id) && (int)$id > 0) {
                $validIds[] = (int)$id;
            }
        }
        
        if (empty($validIds)) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                "`{$this->param_name}` must contain at least one valid positive integer"
            );
        }
        
        if (count($validIds) > 100) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                "`{$this->param_name}` cannot contain more than 100 items"
            );
        }
        
        return $validIds;
    }
}
```

## Using Types in Methods

### Basic Usage

```php
class Users extends AbstractMethod {
    
    public function getUser(PositiveType $id): UserResponse {
        $userId = $id->parse();
        // Use $userId (guaranteed to be positive integer)
    }
    
    public function createUser(EmailType $email, PasswordType $password): UserResponse {
        $emailValue = $email->parse();
        $passwordValue = $password->parse();
        // Use validated values
    }
}
```

### Optional Parameters

```php
class Users extends AbstractMethod {
    
    public function updateUser(
        PositiveType $id, 
        ?string $name = null, 
        ?EmailType $email = null
    ): UserResponse {
        $userId = $id->parse();
        
        if ($name !== null) {
            // Update name
        }
        
        if ($email !== null) {
            $emailValue = $email->parse();
            // Update email
        }
    }
}
```

### Using Enums

```php
enum UserStatus: string {
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case BANNED = 'banned';
}

class Users extends AbstractMethod {
    
    public function updateUserStatus(
        PositiveType $id, 
        UserStatus $status
    ): UserResponse {
        $userId = $id->parse();
        $statusValue = $status->value; // 'active', 'inactive', or 'banned'
        
        // Update user status
    }
}
```

## Type Registration

Types are automatically registered when used in method parameters. The framework uses reflection to detect type requirements:

```php
// The framework automatically detects and instantiates these types:
public function createUser(EmailType $email, PasswordType $password): UserResponse {
    // EmailType and PasswordType are automatically instantiated
    // with the appropriate parameters from the request
}
```

## Error Handling

All types should throw `ErrorException` with appropriate error codes:

```php
use MotionDots\Exception\ErrorException;

class CustomType extends AbstractType {
    
    public function parse(): string {
        if (empty($this->field)) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                "`{$this->param_name}` is required"
            );
        }
        
        if (!is_string($this->field)) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                "`{$this->param_name}` must be a string"
            );
        }
        
        return $this->field;
    }
}
```

## Best Practices

### Type Design

1. **Single Responsibility**: Each type should validate one specific format
2. **Clear Error Messages**: Provide descriptive error messages
3. **Context Usage**: Use context for complex validation logic
4. **Performance**: Keep validation logic efficient
5. **Reusability**: Design types to be reusable across methods

### Validation Rules

1. **Fail Fast**: Validate early and throw exceptions immediately
2. **Clear Messages**: Include parameter names in error messages
3. **Consistent Behavior**: Use the same validation logic across similar types
4. **Edge Cases**: Handle edge cases and boundary conditions

### Error Handling

1. **Appropriate Codes**: Use the correct error code for each situation
2. **Descriptive Messages**: Include context in error messages
3. **Parameter Names**: Always include parameter names in error messages
4. **User-Friendly**: Write error messages for end users, not developers

## Complete Example

```php
<?php
namespace API\Types;

use MotionDots\Type\AbstractType;
use MotionDots\Exception\ErrorException;

class UsernameType extends AbstractType {
    
    private const MIN_LENGTH = 3;
    private const MAX_LENGTH = 20;
    private const PATTERN = '/^[a-zA-Z0-9_]+$/';
    
    public function parse(): string {
        $username = trim((string)$this->field);
        
        if (empty($username)) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                "`{$this->param_name}` is required"
            );
        }
        
        if (strlen($username) < self::MIN_LENGTH) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                "`{$this->param_name}` must be at least " . self::MIN_LENGTH . " characters long"
            );
        }
        
        if (strlen($username) > self::MAX_LENGTH) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                "`{$this->param_name}` cannot be longer than " . self::MAX_LENGTH . " characters"
            );
        }
        
        if (!preg_match(self::PATTERN, $username)) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                "`{$this->param_name}` can only contain letters, numbers, and underscores"
            );
        }
        
        // Check for reserved usernames
        if ($this->isReserved($username)) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                "`{$this->param_name}` is reserved and cannot be used"
            );
        }
        
        return $username;
    }
    
    private function isReserved(string $username): bool {
        $reserved = ['admin', 'root', 'system', 'api', 'www'];
        return in_array(strtolower($username), $reserved);
    }
}
```
