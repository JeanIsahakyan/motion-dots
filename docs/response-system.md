# Response System Documentation

## Overview

The Response System in MotionDots provides a structured way to format API responses. It ensures consistent response formats across your API and automatically handles JSON serialization, type identification, and null value filtering.

## AbstractResponse Class

**File**: `src/Response/AbstractResponse.php`  
**Namespace**: `MotionDots\Response`

The `AbstractResponse` class is the base class for all API response classes. It provides automatic JSON serialization and type identification capabilities.

### Static Methods

#### `create()`

Factory method to create a new response instance.

**Returns:** New instance of the response class

**Example:**
```php
$response = UserResponse::create();
```

### Instance Methods

#### `build(): array`

Builds the response array with automatic serialization and type identification.

**Returns:** Array containing the response data

**Features:**
- Automatically serializes public properties
- Filters out null values
- Adds type identification for frontend consumption
- Handles nested objects and arrays

**Example:**
```php
$response = UserResponse::create()
    ->setId(123)
    ->setName('John Doe')
    ->setEmail('john@example.com');

$data = $response->build();
// Returns: [
//     'id' => 123,
//     'name' => 'John Doe',
//     'email' => 'john@example.com',
//     '__type_id' => 'md5_hash_of_class_name'
// ]
```

## ResponseInterface

**File**: `src/Response/ResponseInterface.php`  
**Namespace**: `MotionDots\Response`

The `ResponseInterface` defines the contract that all response classes must implement.

### Constants

#### `TYPE_ID_FIELD`

```php
public const TYPE_ID_FIELD = '__type_id';
```

Reserved field name for type identification in responses.

### Methods

#### `create(): ResponseInterface`

Static factory method for creating response instances.

#### `build(): array`

Method for building the response array.

## Creating Response Classes

### Basic Response Class

```php
<?php
namespace API\Responses;

use MotionDots\Response\AbstractResponse;

class UserResponse extends AbstractResponse {
    public int $id = 0;
    public string $name = '';
    public string $email = '';
    
    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }
    
    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }
    
    public function setEmail(string $email): self {
        $this->email = $email;
        return $this;
    }
}
```

### Response with Enums

```php
<?php
namespace API\Responses;

use MotionDots\Response\AbstractResponse;
use API\Enums\UserStatus;

class UserResponse extends AbstractResponse {
    public int $id = 0;
    public string $name = '';
    public string $email = '';
    public UserStatus $status;
    
    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }
    
    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }
    
    public function setEmail(string $email): self {
        $this->email = $email;
        return $this;
    }
    
    public function setStatus(UserStatus $status): self {
        $this->status = $status;
        return $this;
    }
}
```

### Complex Response with Nested Objects

```php
<?php
namespace API\Responses;

use MotionDots\Response\AbstractResponse;

class UserProfileResponse extends AbstractResponse {
    public int $id = 0;
    public string $name = '';
    public string $email = '';
    public ?UserSettingsResponse $settings = null;
    public array $permissions = [];
    
    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }
    
    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }
    
    public function setEmail(string $email): self {
        $this->email = $email;
        return $this;
    }
    
    public function setSettings(?UserSettingsResponse $settings): self {
        $this->settings = $settings;
        return $this;
    }
    
    public function setPermissions(array $permissions): self {
        $this->permissions = $permissions;
        return $this;
    }
}

class UserSettingsResponse extends AbstractResponse {
    public bool $notifications = true;
    public string $theme = 'light';
    public string $language = 'en';
    
    public function setNotifications(bool $notifications): self {
        $this->notifications = $notifications;
        return $this;
    }
    
    public function setTheme(string $theme): self {
        $this->theme = $theme;
        return $this;
    }
    
    public function setLanguage(string $language): self {
        $this->language = $language;
        return $this;
    }
}
```

### List Response Pattern

```php
<?php
namespace API\Responses;

use MotionDots\Response\AbstractResponse;

class ListResponse extends AbstractResponse {
    public int $count = 0;
    public array $items = [];
    public ?int $total = null;
    public ?int $page = null;
    public ?int $perPage = null;
    
    public function setCount(int $count): self {
        $this->count = $count;
        return $this;
    }
    
    public function setItems(array $items): self {
        $this->items = $items;
        return $this;
    }
    
    public function setTotal(?int $total): self {
        $this->total = $total;
        return $this;
    }
    
    public function setPage(?int $page): self {
        $this->page = $page;
        return $this;
    }
    
    public function setPerPage(?int $perPage): self {
        $this->perPage = $perPage;
        return $this;
    }
}
```

## Using Responses in Methods

### Basic Usage

```php
class Users extends AbstractMethod {
    
    public function getUser(PositiveType $id): UserResponse {
        $userId = $id->parse();
        
        // Business logic
        $user = $this->findUser($userId);
        
        return UserResponse::create()
            ->setId($userId)
            ->setName($user['name'])
            ->setEmail($user['email']);
    }
}
```

### List Responses

```php
class Users extends AbstractMethod {
    
    public function getUsers(PositiveListType $ids): ListResponse {
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
}
```

### Complex Responses

```php
class Users extends AbstractMethod {
    
    public function getUserProfile(PositiveType $id): UserProfileResponse {
        $userId = $id->parse();
        
        // Get user data
        $user = $this->findUser($userId);
        $settings = $this->getUserSettings($userId);
        $permissions = $this->getUserPermissions($userId);
        
        return UserProfileResponse::create()
            ->setId($userId)
            ->setName($user['name'])
            ->setEmail($user['email'])
            ->setSettings(
                UserSettingsResponse::create()
                    ->setNotifications($settings['notifications'])
                    ->setTheme($settings['theme'])
                    ->setLanguage($settings['language'])
            )
            ->setPermissions($permissions);
    }
}
```

## Response Building Process

### Automatic Serialization

The `build()` method automatically handles:

1. **Property Serialization**: Converts all public properties to array format
2. **Null Filtering**: Removes null values from the response
3. **Type Identification**: Adds `__type_id` field with MD5 hash of class name
4. **Nested Object Handling**: Recursively processes nested response objects

### Type Identification

Each response includes a `__type_id` field that contains an MD5 hash of the class name:

```php
$response = UserResponse::create()->setId(123);
$data = $response->build();
// $data['__type_id'] = 'a1b2c3d4e5f6...' (MD5 of 'UserResponse')
```

This allows frontend applications to identify response types for proper handling.

### Null Value Handling

Null values are automatically filtered out of responses:

```php
$response = UserResponse::create()
    ->setId(123)
    ->setName('John Doe')
    ->setEmail(null); // This will be filtered out

$data = $response->build();
// Returns: ['id' => 123, 'name' => 'John Doe', '__type_id' => '...']
```

## Response Patterns

### Success Response

```php
// Method returns response object
public function getUser(PositiveType $id): UserResponse {
    return UserResponse::create()->setId($id->parse());
}

// Framework wraps in success structure
{
    "response": {
        "id": 123,
        "name": "John Doe",
        "__type_id": "a1b2c3d4e5f6..."
    }
}
```

### Error Response

```php
// Method throws exception
public function getUser(PositiveType $id): UserResponse {
    throw new ErrorException(ErrorException::PARAM_INCORRECT, "User not found");
}

// Framework wraps in error structure
{
    "error": {
        "error_code": -9,
        "error_message": "User not found"
    }
}
```

### List Response

```php
// Method returns list response
public function getUsers(): ListResponse {
    return ListResponse::create()
        ->setItems([...])
        ->setCount(10);
}

// Framework wraps in success structure
{
    "response": {
        "count": 10,
        "items": [...],
        "__type_id": "b2c3d4e5f6a1..."
    }
}
```

## Best Practices

### Response Design

1. **Consistent Structure**: Use the same response patterns across your API
2. **Type Safety**: Use typed properties for better IDE support
3. **Fluent Interface**: Use method chaining for better readability
4. **Null Handling**: Design responses to handle optional data gracefully

### Property Design

1. **Public Properties**: Only public properties are serialized
2. **Default Values**: Set appropriate default values
3. **Type Hints**: Use proper type hints for better validation
4. **Documentation**: Document complex response structures

### Method Design

1. **Single Responsibility**: Each response class should represent one concept
2. **Reusability**: Design responses to be reusable across methods
3. **Extensibility**: Allow for easy extension with new properties
4. **Validation**: Validate data before setting properties

### Error Handling

1. **Consistent Errors**: Use the same error response format
2. **Descriptive Messages**: Provide clear error messages
3. **Error Codes**: Use appropriate error codes
4. **Context**: Include relevant context in error responses

## Complete Example

```php
<?php
namespace API\Responses;

use MotionDots\Response\AbstractResponse;
use API\Enums\UserStatus;

class UserResponse extends AbstractResponse {
    public int $id = 0;
    public string $name = '';
    public string $email = '';
    public UserStatus $status;
    public ?string $avatar = null;
    public ?UserProfileResponse $profile = null;
    public array $roles = [];
    public ?\DateTime $createdAt = null;
    public ?\DateTime $updatedAt = null;
    
    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }
    
    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }
    
    public function setEmail(string $email): self {
        $this->email = $email;
        return $this;
    }
    
    public function setStatus(UserStatus $status): self {
        $this->status = $status;
        return $this;
    }
    
    public function setAvatar(?string $avatar): self {
        $this->avatar = $avatar;
        return $this;
    }
    
    public function setProfile(?UserProfileResponse $profile): self {
        $this->profile = $profile;
        return $this;
    }
    
    public function setRoles(array $roles): self {
        $this->roles = $roles;
        return $this;
    }
    
    public function setCreatedAt(?\DateTime $createdAt): self {
        $this->createdAt = $createdAt;
        return $this;
    }
    
    public function setUpdatedAt(?\DateTime $updatedAt): self {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}

// Usage in method
class Users extends AbstractMethod {
    
    public function getUser(PositiveType $id): UserResponse {
        $userId = $id->parse();
        $user = $this->findUser($userId);
        
        return UserResponse::create()
            ->setId($userId)
            ->setName($user['name'])
            ->setEmail($user['email'])
            ->setStatus(UserStatus::ACTIVE)
            ->setAvatar($user['avatar'])
            ->setRoles($user['roles'])
            ->setCreatedAt($user['created_at'])
            ->setUpdatedAt($user['updated_at']);
    }
}
```

## Response Serialization

The framework automatically handles response serialization through the `ResponseBuilder` class:

1. **Object Detection**: Identifies response objects
2. **Method Calling**: Calls `build()` method on response objects
3. **Recursive Processing**: Handles nested objects and arrays
4. **Type Conversion**: Converts enums and other special types
5. **Final Wrapping**: Wraps the result in the standard response format

This ensures that all responses follow the same structure and are properly serialized for JSON output.
