
# MotionDots

**MotionDots** is a lightweight PHP framework designed to simplify API development by providing tools for dynamic method invocation, input validation, structured responses, and TypeScript generation. It streamlines the process of building APIs by handling common tasks such as parameter validation, response formatting, error handling, and supports the use of native PHP 8.1 enums for robust type definitions.

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue.svg)](https://www.php.net/)

## ✨ Key Features

-   **🚀 Dynamic Method Invocation**: Automatically maps API requests to methods in your classes
-   **🛡️ Type Safety**: Validates and sanitizes input parameters using custom types
-   **📋 Structured Responses**: Ensures consistent response formats across your API
-   **⚡ Error Handling**: Comprehensive error system with predefined error codes
-   **🔧 Enum Support**: Native PHP 8.1 enums for robust type definitions
-   **🔄 Context Management**: Shares data across methods and types during a request
-   **📱 TypeScript Generation**: Automatic TypeScript definitions for frontend integration
-   **🔍 System Methods**: Built-in introspection and utility methods

## 🎯 Perfect For

-   **API Developers** looking to quickly build robust APIs
-   **Projects** requiring strict input validation and type safety
-   **Teams** needing consistent response formats across endpoints
-   **Applications** where dynamic method routing is beneficial
-   **Frontend Teams** requiring TypeScript integration
-   **Systems** needing shared context between methods and types

## 📚 Documentation

For comprehensive documentation, visit our [Documentation Index](docs/index.md):

- **[Quick Start Guide](docs/README.md)** - Get up and running quickly
- **[Architecture Overview](docs/architecture.md)** - Understand the system design
- **[API Reference](docs/api-reference.md)** - Complete API documentation
- **[Examples & Patterns](docs/examples.md)** - Real-world usage examples
- **[TypeScript Integration](docs/typescript-generation.md)** - Frontend type generation

----------

## 🚀 Quick Start

### Requirements

- **PHP 8.1+** (required for enum support and modern features)
- **Composer** for dependency management
- **JSON extension** (usually included with PHP)

### Installation

Install MotionDots via Composer:

```bash
composer require jeanisahakyan/motion-dots
```

### Basic Setup

Create a simple API endpoint in just a few lines:

```php
<?php
require_once 'vendor/autoload.php';

use MotionDots\Process\Processor;
use MotionDots\Schema\Schema;
use API\Methods\Users;

// Create schema and add methods
$schema = Schema::create()->addMethods([new Users()]);

// Create processor
$processor = new Processor($schema, '.');

// Handle request
$response = $processor->invokeProcess('users.getUser', $params);
echo json_encode($response);
```

> 📖 **Need more details?** Check out our [Complete Quick Start Guide](docs/README.md) for detailed setup instructions.

### Project Structure

```
project/
├── composer.json
├── vendor/
│   └── autoload.php
├── index.php
├── docs/                    # 📚 Comprehensive documentation
└── src/
    ├── API/
    │   ├── Methods/         # 🚀 API endpoint classes
    │   │   └── Users.php
    │   ├── Responses/       # 📋 Response format classes
    │   │   └── UserResponse.php
    │   ├── Types/          # 🛡️ Custom parameter types
    │   │   ├── EmailType.php
    │   │   └── PasswordType.php
    │   └── Enums/          # 🔧 PHP 8.1 enums
    │       └── UserStatus.php
    └── (Other application files)
```

## 🔧 Core Concepts

### API Methods

Create API endpoints by extending `AbstractMethod`:

```php
class Users extends AbstractMethod {
    public function getUser(PositiveType $id): UserResponse {
        $userId = $id->parse();
        return UserResponse::create()->setId($userId);
    }
}
```

### Custom Types

Build type-safe parameters with validation:

```php
class EmailType extends AbstractType {
    public function parse(): string {
        $email = filter_var($this->field, FILTER_VALIDATE_EMAIL);
        if ($email === false) {
            throw new ErrorException(ErrorException::PARAM_INCORRECT, "Invalid email");
        }
        return $email;
    }
}
```

### Response Classes

Structure your API responses:

```php
class UserResponse extends AbstractResponse {
    public int $id = 0;
    public string $name = '';
    
    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }
}
```

> 📖 **Learn more:** See our [Method System](docs/method-system.md), [Type System](docs/type-system.md), and [Response System](docs/response-system.md) documentation.

## 🛠️ Advanced Setup

### Complete API Processor

```php
<?php
// index.php
require_once 'vendor/autoload.php';

use MotionDots\Process\Processor;
use MotionDots\Schema\Schema;
use API\Methods\Users;

// Set headers
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=UTF-8');

try {
    // Merge request parameters
    $params = array_merge($_GET, $_POST, $_FILES);
    
    // Create schema and add methods
    $schema = Schema::create()->addMethods([
        new Users(),
        // Add other method classes here
    ]);
    
    // Create processor
    $processor = new Processor($schema, '.');
    
    // Set initial context
    $processor->getContext()->setMany([
        'requestTime' => microtime(true),
        'clientIp' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
    
    // Extract method from URL
    if (preg_match('/\/api\/([a-zA-Z\.]+)/i', $_SERVER['REQUEST_URI'], $matches)) {
        [, $method] = $matches;
    } else {
        $method = 'system.getSchema'; // Default to schema info
    }
    
    // Invoke method and return response
    $response = $processor->invokeProcess($method, $params);
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


## 📖 Detailed Examples

### Creating API Methods

API methods are organized into classes extending `AbstractMethod`. Each public method becomes an API endpoint.

```php
<?php
// src/API/Methods/Users.php
namespace API\Methods;

use MotionDots\Method\AbstractMethod;
use API\Responses\UserResponse;
use API\Types\EmailType;
use MotionDots\Type\PositiveType;

class Users extends AbstractMethod {
    public function getUser(PositiveType $id): UserResponse {
        $userId = $id->parse();
        return UserResponse::create()->setId($userId);
    }
    
    public function createUser(EmailType $email, string $name): UserResponse {
        $emailValue = $email->parse();
        return UserResponse::create()
            ->setEmail($emailValue)
            ->setName($name);
    }
}
```

### Implementing API Methods

Define public methods in your class using camelCase. The method's name, combined with the class name, forms the API endpoint.

#### Method: `registerUser`

**Definition:**
```php
public function registerUser(EmailType $email, PasswordType $password): UserResponse {
    $emailValue = $email->parse();
    $passwordValue = $password->parse();

    // Business logic here

    return UserResponse::create()
        ->setEmail($emailValue)
        ->setStatus(UserStatus::ACTIVE);
}
```
**Usage of Context in Method:**
```php
public function registerUser(EmailType $email, PasswordType $password): UserResponse {
    // Set data in context
    $request_time = $this->context->get('requestTime');

    // Rest of the method...
}
```
**Request Example:**
```http
POST /api/users.registerUser
Content-Type: application/x-www-form-urlencoded

email=jane.doe@example.com&password=SecurePass123
```
**Response Example:**
```json
{
    "response": {
        "id": 1,
        "email": "jane.doe@example.com",
        "status": "active"
    }
}
```

#### Method: `loginUser`

**Definition:**
```php
public function loginUser(EmailType $email, PasswordType $password): UserResponse {
    $emailValue = $email->parse();
    $passwordValue = $password->parse();

    // Business logic here

    // Set user ID in context after successful login
    $this->context->set('userId', $userId);

    return UserResponse::create()
        ->setId($userId)
        ->setEmail($emailValue)
        ->setStatus(UserStatus::ACTIVE);
}
```

**Request Example:**
```http
POST /api/users.loginUser
Content-Type: application/x-www-form-urlencoded

email=jane.doe@example.com&password=SecurePass123
```

**Response Example:**
```json
{
    "response": {
        "id": 1,
        "email": "jane.doe@example.com",
        "status": "active"
    }
}
```

#### Method: `updateUserStatus`

**Definition:**
```php
public function updateUserStatus(int $userId, UserStatus $status): UserResponse {
    // Access user ID from context if needed
    $currentUserId = $this->context->get('userId');

    // Business logic here

    return UserResponse::create()
        ->setId($userId)
        ->setStatus($status);
}
```
**Request Example:**
```http
POST /api/users.updateUserStatus
Content-Type: application/x-www-form-urlencoded

userId=1&status=inactive
```

**Response Example:**
```json
{
    "response": {
        "id": 1,
        "email": "jane.doe@example.com",
        "status": "inactive"
    }
}
```

## Creating a New Response

Responses extend `AbstractResponse` and define the structure of the data returned to the client. The `AbstractResponse` class automatically handles JSON serialization of public properties.

**Example:**
```php
<?php
// src/API/Responses/UserResponse.php

namespace API\Responses;

use MotionDots\Response\AbstractResponse;
use API\Enums\UserStatus;

class UserResponse extends AbstractResponse {
    public int $id;
    public string $email;
    public UserStatus $status;

    public function setId(int $id): self {
        $this->id = $id;
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
**Note:** The `AbstractResponse` class provides default implementations for JSON serialization by automatically including public properties.

## Creating a New Parameter Type

Custom parameter types extend `AbstractType` and handle validation and parsing of input parameters. They have access to the context via `$this->context`.

**Example:**
```php
<?php
// src/API/Types/EmailType.php

namespace API\Types;

use MotionDots\Type\AbstractType;
use MotionDots\Exception\ErrorException;

class EmailType extends AbstractType {
    public function parse(): string {
        $email = filter_var($this->field, FILTER_VALIDATE_EMAIL);
        if ($email === false) {
            throw new ErrorException(ErrorException::PARAM_INCORRECT, "`{$this->param_name}` must be a valid email address");
        }
        return $email;
    }
}
```

## Using Enums

Enums provide a way to define a set of named constants, which can be used for parameter validation and response fields. With PHP 8.1, you can use native enums.

### Defining an Enum

**Example:**
```php
<?php
// src/API/Enums/UserStatus.php

namespace API\Enums;

enum UserStatus: string {
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case BANNED = 'banned';
}
```
### Using Enums in Parameters

Enums can be used as parameter types to ensure that only valid values are passed.

**Usage in Method:**
```php
public function updateUserStatus(int $userId, UserStatus $status): UserResponse {
    // Business logic here

    return UserResponse::create()
        ->setId($userId)
        ->setStatus($status);
}
```

## Handling API Requests

Requests are handled by the `Processor`, which invokes the appropriate method based on the request URI.
```php
  // Instantiate the processor
    $processor = new Processor($schema, '.');
    // Invoke the method and output the response
    $response = $processor->invokeProcess($method, $params);
```

## Error Handling

### Throwing Errors

To throw errors in your methods, use the `ErrorException` class.

**Example:**
```php
use MotionDots\Exception\ErrorException;

if (!$user) {
    throw new ErrorException(ErrorException::PARAM_INCORRECT, "User not found");
}

```
**Common Error Codes:**
-   `ErrorException::SCHEMA_METHOD_EXISTS ` (-1)
-   `ErrorException::PARAM_UNSUPPORTED` (-2)
-   `ErrorException::PARAM_UNKNOWN_RESOLVER` (-3)
-   `ErrorException::PARAM_REFLECTION_ERROR` (-4)
-   `ErrorException::PARAM_IS_REQUIRED` (-5)
-   `ErrorException::CONTEXT_UNDEFINED_FIELD` (-6)
-   `ErrorException::METHOD_ACTION_UNDEFINED` (-7)
-   `ErrorException::METHOD_UNDEFINED` (-8)
-   `ErrorException::PARAM_INCORRECT` (-9)
-   `ErrorException::INTERNAL_ERROR` (-10)
-   (Add other error codes as needed)

### Handling Errors in the Processor

Errors thrown in your methods are caught in the `YourProcessor.php` and returned as structured error responses.
**Example Error Response:**
```json
{
    "error": {
        "error_code": -9,
        "error_message": "User not found"
    }
}
```

## 📱 TypeScript Generation

Generate TypeScript definitions automatically for frontend integration:

```php
<?php
use MotionDots\Schema\Typescript\Generator;

// Generate TypeScript definitions
Generator::create()
    ->setFilesPath('./frontend/src/types/api')
    ->setIsVerbose(true)
    ->excludeSpaces('admin', 'debug')
    ->generate($processor);
```

This creates organized TypeScript files:
```
frontend/src/types/api/
├── methods/
│   ├── users.d.ts
│   └── index.d.ts
├── responses/
│   ├── UserResponse.d.ts
│   └── index.d.ts
└── enums/
    ├── UserStatus.d.ts
    └── index.d.ts
```

## ⚡ Error Handling

MotionDots provides comprehensive error handling with predefined error codes:

```php
use MotionDots\Exception\ErrorException;

// Throw specific errors
throw new ErrorException(
    ErrorException::PARAM_INCORRECT,
    "User with ID {$userId} not found"
);
```

**Common Error Codes:**
- `PARAM_INCORRECT` (-9) - Parameter validation failed
- `PARAM_IS_REQUIRED` (-5) - Required parameter missing
- `METHOD_UNDEFINED` (-8) - Method not registered
- `INTERNAL_ERROR` (-10) - System error

## 🎯 What's Next?

Ready to dive deeper? Check out our comprehensive documentation:

- **[📚 Complete Documentation](docs/index.md)** - Full documentation index
- **[🚀 Quick Start Guide](docs/README.md)** - Detailed setup instructions
- **[🏗️ Architecture Overview](docs/architecture.md)** - System design and patterns
- **[📖 API Reference](docs/api-reference.md)** - Complete API documentation
- **[💡 Examples & Patterns](docs/examples.md)** - Real-world usage examples
- **[🔧 TypeScript Integration](docs/typescript-generation.md)** - Frontend type generation
- **[🛡️ Type System](docs/type-system.md)** - Custom types and validation
- **[📋 Response System](docs/response-system.md)** - Response formatting and serialization

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request. For major changes, please open an issue first to discuss what you would like to change.

## 📞 Support

- 📖 **Documentation**: Check our [comprehensive docs](docs/index.md)
- 🐛 **Issues**: Report bugs on GitHub Issues
- 💡 **Questions**: Open a discussion for questions and ideas

---

**MotionDots** - Building robust APIs with PHP 8.1+ features, type safety, and automatic TypeScript generation. 🚀
