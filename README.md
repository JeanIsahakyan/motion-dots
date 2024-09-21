
# MotionDots Documentation

**MotionDots** is a PHP framework designed to simplify API development by providing tools for dynamic method invocation, input validation, structured responses, and more. It streamlines the process of building APIs by handling common tasks such as parameter validation, response formatting, error handling, and supports the use of native PHP 8.1 enums for robust type definitions.

----------

## What is MotionDots?


**MotionDots** is a lightweight PHP framework that aids in developing APIs by providing:
-   **Dynamic Method Invocation**: Automatically maps API requests to methods in your classes.
-   **Input Validation**: Validates and sanitizes input parameters using custom types.
-   **Structured Responses**: Ensures consistent response formats across your API.
-   **Error Handling**: Simplifies error reporting and handling.
-   **Enum Support**: Utilizes native PHP 8.1 enums for robust type definitions.
-   **Context Management**: Shares data across methods and types during a request.

### Purpose and Use Cases

MotionDots is ideal for:

-   Developers looking to quickly build robust APIs.
-   Projects requiring strict input validation.
-   APIs that need consistent response formats.
-   Applications where dynamic method routing is beneficial.
-   Projects that can benefit from the use of enums for parameter and response type definitions.
-   Applications needing a shared context between methods and types.

----------

## Getting Started with MotionDots

### Installation via Composer

Ensure your PHP version is **8.1** or higher.

Install MotionDots via Composer by adding it to your `composer.json` or running:

```
composer require jeanisahakyan/motion-dots
```

### Recommended Project Structure

Organize your project as follows:
```
project/
├── composer.json
├── vendor/
│   └── autoload.php
├── index.php
└── src/
    ├── API/
    │   ├── Methods/
    │   │   └── Users.php
    │   ├── Responses/
    │   │   └── UserResponse.php
    │   ├── Types/
    │   │   ├── EmailType.php
    │   │   ├── PasswordType.php
    │   └── Type/
    │      └── UserStatus.php
    ├── YourProcessor.php
    └── (Other application files)
```

### Creating the API Processor

The API processor handles incoming requests and routes them to the appropriate methods. Place it in `src/YourProcessor.php`.

**Example `YourProcessor.php`:**
```php
<?php
// src/YourProcessor.php

namespace YourNamespace;

use MotionDots\Processor\Processor;
use MotionDots\Schema\Schema;
use API\Methods\Users;
use MotionDots\Method\System\System;

class YourProcessor {
    public function handleRequest() {
        // Set headers
        header("Access-Control-Allow-Origin: *");
        header('Content-Type: application/json; charset=UTF-8');

        try {
            // Merge GET, POST, and FILES parameters
            $params = array_merge($_GET, $_POST, $_FILES);

            // Create a schema and add methods
            $schema = Schema::create()->addMethods([
                new Users(),
                // Add other method classes here
            ]);

            // Instantiate the processor
            $processor = new Processor($schema, '.');

            // Set initial context values if needed
            $processor->getContext()->setMany([
                'requestStartTime' => microtime(true),
                // Add other context variables here
            ]);

            // Determine the method to invoke
            if (preg_match('/\/api\/([a-zA-Z\.]+)/i', $_SERVER['REQUEST_URI'], $matches)) {
                [, $method] = $matches;
            } else {
                $method = 'system.getSchema'; // Default method
            }

            // Invoke the method and output the response
            $response = $processor->invokeProcess($method, $params);
            echo json_encode($response);
        } catch (\Exception $exception) {
            // Handle exceptions and output error response
            echo json_encode([
                'error' => [
                    'error_code'    => $exception->getCode(),
                    'error_message' => $exception->getMessage(),
                ]
            ]);
        }
    }
}
```
**Explanation:**

-   **Setting Context Values:**
    -   After instantiating the `Processor`, you can access the `Context` object using `$processor->getContext()`.
    -   Use `set()` or `setMany()` methods to set initial context values.
    -   These values will be accessible in your methods and types during the request lifecycle.

Then, in your `index.php`, you can instantiate and use this processor:

**Example `index.php`:**
```php
<?php
require_once 'vendor/autoload.php';

use YourNamespace\YourProcessor;

$processor = new YourProcessor();
$processor->handleRequest();
```


## Creating a New API Method

API methods are organized into classes extending `AbstractMethod`. Each public method in the class becomes an API endpoint.

### Defining the Method Class

Create a new class in `src/API/Methods/`.

**Example:**
```php
<?php
// src/API/Methods/Users.php

namespace API\Methods;

use MotionDots\Method\AbstractMethod;
use API\Responses\UserResponse;
use API\Types\EmailType;
use API\Types\PasswordType;
use API\Enums\UserStatus;

class Users extends AbstractMethod {
    // Methods will be defined here
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
