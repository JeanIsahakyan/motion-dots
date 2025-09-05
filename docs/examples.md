# Examples and Usage Guides

## Table of Contents

- [Quick Start Example](#quick-start-example)
- [Complete API Example](#complete-api-example)
- [Advanced Usage Patterns](#advanced-usage-patterns)
- [Real-World Scenarios](#real-world-scenarios)
- [Integration Examples](#integration-examples)
- [Best Practices Examples](#best-practices-examples)

## Quick Start Example

### Basic Setup

Create a simple API with user management:

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

### User Method Class

```php
<?php
// src/API/Methods/Users.php
namespace API\Methods;

use MotionDots\Method\AbstractMethod;
use MotionDots\Exception\ErrorException;
use API\Responses\UserResponse;
use MotionDots\Type\PositiveType;

class Users extends AbstractMethod {
    
    public function getUser(PositiveType $id): UserResponse {
        $userId = $id->parse();
        
        // Simulate user lookup
        $user = $this->findUser($userId);
        if (!$user) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                "User with ID {$userId} not found"
            );
        }
        
        return UserResponse::create()
            ->setId($userId)
            ->setName($user['name'])
            ->setEmail($user['email']);
    }
    
    private function findUser(int $id): ?array {
        // Simulate database lookup
        $users = [
            1 => ['name' => 'John Doe', 'email' => 'john@example.com'],
            2 => ['name' => 'Jane Smith', 'email' => 'jane@example.com'],
            3 => ['name' => 'Bob Johnson', 'email' => 'bob@example.com'],
        ];
        
        return $users[$id] ?? null;
    }
}
```

### User Response Class

```php
<?php
// src/API/Responses/UserResponse.php
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

### Testing the API

```bash
# Get user by ID
curl -X POST "http://localhost/api/users.getUser" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "id=1"

# Response:
{
  "response": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "__type_id": "a1b2c3d4e5f6..."
  }
}
```

## Complete API Example

### E-commerce API Structure

```
src/
├── API/
│   ├── Methods/
│   │   ├── Users.php
│   │   ├── Products.php
│   │   ├── Orders.php
│   │   └── Categories.php
│   ├── Responses/
│   │   ├── UserResponse.php
│   │   ├── ProductResponse.php
│   │   ├── OrderResponse.php
│   │   ├── CategoryResponse.php
│   │   └── ListResponse.php
│   ├── Types/
│   │   ├── EmailType.php
│   │   ├── PasswordType.php
│   │   └── PriceType.php
│   └── Enums/
│       ├── UserStatus.php
│       ├── OrderStatus.php
│       └── ProductCategory.php
```

### Enums

```php
<?php
// src/API/Enums/UserStatus.php
namespace API\Enums;

enum UserStatus: string {
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case BANNED = 'banned';
    case PENDING = 'pending';
}
```

```php
<?php
// src/API/Enums/OrderStatus.php
namespace API\Enums;

enum OrderStatus: string {
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
}
```

### Custom Types

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
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                "`{$this->param_name}` must be a valid email address"
            );
        }
        return $email;
    }
}
```

```php
<?php
// src/API/Types/PasswordType.php
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

### Response Classes

```php
<?php
// src/API/Responses/UserResponse.php
namespace API\Responses;

use MotionDots\Response\AbstractResponse;
use API\Enums\UserStatus;

class UserResponse extends AbstractResponse {
    public int $id = 0;
    public string $name = '';
    public string $email = '';
    public UserStatus $status;
    public ?string $avatar = null;
    public ?\DateTime $createdAt = null;
    
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
    
    public function setCreatedAt(?\DateTime $createdAt): self {
        $this->createdAt = $createdAt;
        return $this;
    }
}
```

```php
<?php
// src/API/Responses/ListResponse.php
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

### Method Classes

```php
<?php
// src/API/Methods/Users.php
namespace API\Methods;

use MotionDots\Method\AbstractMethod;
use MotionDots\Exception\ErrorException;
use API\Responses\UserResponse;
use API\Responses\ListResponse;
use API\Types\EmailType;
use API\Types\PasswordType;
use API\Enums\UserStatus;
use MotionDots\Type\PositiveType;
use MotionDots\Type\PositiveListType;

class Users extends AbstractMethod {
    
    public function getUser(PositiveType $id): UserResponse {
        $userId = $id->parse();
        $user = $this->findUser($userId);
        
        if (!$user) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                "User with ID {$userId} not found"
            );
        }
        
        return UserResponse::create()
            ->setId($userId)
            ->setName($user['name'])
            ->setEmail($user['email'])
            ->setStatus($user['status'])
            ->setAvatar($user['avatar'])
            ->setCreatedAt($user['created_at']);
    }
    
    public function getUsers(PositiveListType $ids): ListResponse {
        $userIds = $ids->parse();
        $users = [];
        
        foreach ($userIds as $userId) {
            $user = $this->findUser($userId);
            if ($user) {
                $users[] = UserResponse::create()
                    ->setId($userId)
                    ->setName($user['name'])
                    ->setEmail($user['email'])
                    ->setStatus($user['status']);
            }
        }
        
        return ListResponse::create()
            ->setItems($users)
            ->setCount(count($users));
    }
    
    public function createUser(
        string $name, 
        EmailType $email, 
        PasswordType $password
    ): UserResponse {
        $emailValue = $email->parse();
        $passwordValue = $password->parse();
        
        // Check if user already exists
        if ($this->userExists($emailValue)) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                "User with email {$emailValue} already exists"
            );
        }
        
        // Create user
        $userId = $this->saveUser($name, $emailValue, $passwordValue);
        
        // Set context data
        $this->context->set('createdUserId', $userId);
        
        return UserResponse::create()
            ->setId($userId)
            ->setName($name)
            ->setEmail($emailValue)
            ->setStatus(UserStatus::ACTIVE)
            ->setCreatedAt(new \DateTime());
    }
    
    public function updateUserStatus(
        PositiveType $id, 
        UserStatus $status
    ): UserResponse {
        $userId = $id->parse();
        $user = $this->findUser($userId);
        
        if (!$user) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                "User with ID {$userId} not found"
            );
        }
        
        // Update status
        $this->updateUserStatus($userId, $status);
        
        return UserResponse::create()
            ->setId($userId)
            ->setName($user['name'])
            ->setEmail($user['email'])
            ->setStatus($status);
    }
    
    private function findUser(int $id): ?array {
        // Simulate database lookup
        $users = [
            1 => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'status' => UserStatus::ACTIVE,
                'avatar' => 'https://example.com/avatar1.jpg',
                'created_at' => new \DateTime('2023-01-01')
            ],
            2 => [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'status' => UserStatus::ACTIVE,
                'avatar' => null,
                'created_at' => new \DateTime('2023-01-02')
            ],
        ];
        
        return $users[$id] ?? null;
    }
    
    private function userExists(string $email): bool {
        // Simulate email check
        return in_array($email, ['john@example.com', 'jane@example.com']);
    }
    
    private function saveUser(string $name, string $email, string $password): int {
        // Simulate user creation
        return 3;
    }
    
    private function updateUserStatus(int $id, UserStatus $status): void {
        // Simulate status update
    }
}
```

### Main API File

```php
<?php
// index.php
require_once 'vendor/autoload.php';

use MotionDots\Process\Processor;
use MotionDots\Schema\Schema;
use API\Methods\Users;
use API\Methods\Products;
use API\Methods\Orders;

// Set headers
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type');
header('Content-Type: application/json; charset=UTF-8');

try {
    // Merge request parameters
    $params = array_merge($_GET, $_POST, $_FILES);
    
    // Create schema and add methods
    $schema = Schema::create()->addMethods([
        new Users(),
        new Products(),
        new Orders(),
    ]);
    
    // Create processor
    $processor = new Processor($schema, '.');
    
    // Set initial context
    $processor->getContext()->setMany([
        'requestTime' => microtime(true),
        'clientIp' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'userAgent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ]);
    
    // Extract method from URL
    if (preg_match('/\/api\/([a-zA-Z\.]+)/i', $_SERVER['REQUEST_URI'], $matches)) {
        [, $method] = $matches;
    } else {
        $method = 'system.getSchema';
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

## Advanced Usage Patterns

### Middleware Pattern

```php
<?php
// src/Middleware/AuthMiddleware.php
namespace API\Middleware;

use MotionDots\Process\Context;
use MotionDots\Exception\ErrorException;

class AuthMiddleware {
    
    public static function authenticate(Context $context): int {
        $token = $context->get('authToken');
        
        if (!$token) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                'Authentication token required'
            );
        }
        
        $userId = self::validateToken($token);
        if (!$userId) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                'Invalid authentication token'
            );
        }
        
        $context->set('currentUserId', $userId);
        return $userId;
    }
    
    private static function validateToken(string $token): ?int {
        // Token validation logic
        return 123; // Simulate valid user ID
    }
}
```

### Using Middleware in Methods

```php
<?php
// src/API/Methods/ProtectedUsers.php
namespace API\Methods;

use MotionDots\Method\AbstractMethod;
use API\Middleware\AuthMiddleware;
use API\Responses\UserResponse;
use MotionDots\Type\PositiveType;

class ProtectedUsers extends AbstractMethod {
    
    public function getCurrentUser(): UserResponse {
        // Authenticate user
        $userId = AuthMiddleware::authenticate($this->context);
        
        // Get user data
        $user = $this->findUser($userId);
        
        return UserResponse::create()
            ->setId($userId)
            ->setName($user['name'])
            ->setEmail($user['email']);
    }
    
    public function updateProfile(
        ?string $name = null,
        ?string $email = null
    ): UserResponse {
        // Authenticate user
        $userId = AuthMiddleware::authenticate($this->context);
        
        // Update profile logic
        $user = $this->updateUser($userId, $name, $email);
        
        return UserResponse::create()
            ->setId($userId)
            ->setName($user['name'])
            ->setEmail($user['email']);
    }
    
    private function findUser(int $id): array {
        // User lookup logic
        return ['name' => 'John Doe', 'email' => 'john@example.com'];
    }
    
    private function updateUser(int $id, ?string $name, ?string $email): array {
        // User update logic
        return ['name' => $name ?? 'John Doe', 'email' => $email ?? 'john@example.com'];
    }
}
```

### Caching Pattern

```php
<?php
// src/API/Methods/CachedProducts.php
namespace API\Methods;

use MotionDots\Method\AbstractMethod;
use API\Responses\ProductResponse;
use MotionDots\Type\PositiveType;

class CachedProducts extends AbstractMethod {
    
    private static array $cache = [];
    
    public function getProduct(PositiveType $id): ProductResponse {
        $productId = $id->parse();
        
        // Check cache first
        if (isset(self::$cache[$productId])) {
            $this->context->set('fromCache', true);
            return self::$cache[$productId];
        }
        
        // Load from database
        $product = $this->loadProduct($productId);
        
        // Cache the result
        self::$cache[$productId] = $product;
        
        $this->context->set('fromCache', false);
        return $product;
    }
    
    private function loadProduct(int $id): ProductResponse {
        // Simulate database load
        return ProductResponse::create()
            ->setId($id)
            ->setName("Product {$id}")
            ->setPrice(99.99);
    }
}
```

## Real-World Scenarios

### E-commerce Order Processing

```php
<?php
// src/API/Methods/Orders.php
namespace API\Methods;

use MotionDots\Method\AbstractMethod;
use MotionDots\Exception\ErrorException;
use API\Responses\OrderResponse;
use API\Enums\OrderStatus;
use MotionDots\Type\PositiveType;
use MotionDots\Type\PositiveListType;

class Orders extends AbstractMethod {
    
    public function createOrder(
        PositiveListType $productIds,
        string $customerName,
        string $customerEmail
    ): OrderResponse {
        $productIds = $productIds->parse();
        
        // Validate products exist
        $products = $this->validateProducts($productIds);
        
        // Calculate total
        $total = $this->calculateTotal($products);
        
        // Create order
        $orderId = $this->saveOrder($productIds, $customerName, $customerEmail, $total);
        
        // Set context for other methods
        $this->context->set('lastOrderId', $orderId);
        $this->context->set('orderTotal', $total);
        
        return OrderResponse::create()
            ->setId($orderId)
            ->setCustomerName($customerName)
            ->setCustomerEmail($customerEmail)
            ->setTotal($total)
            ->setStatus(OrderStatus::PENDING)
            ->setCreatedAt(new \DateTime());
    }
    
    public function updateOrderStatus(
        PositiveType $id,
        OrderStatus $status
    ): OrderResponse {
        $orderId = $id->parse();
        
        // Validate order exists
        $order = $this->findOrder($orderId);
        if (!$order) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                "Order with ID {$orderId} not found"
            );
        }
        
        // Update status
        $this->updateOrderStatus($orderId, $status);
        
        return OrderResponse::create()
            ->setId($orderId)
            ->setCustomerName($order['customer_name'])
            ->setCustomerEmail($order['customer_email'])
            ->setTotal($order['total'])
            ->setStatus($status)
            ->setUpdatedAt(new \DateTime());
    }
    
    private function validateProducts(array $productIds): array {
        $products = [];
        foreach ($productIds as $id) {
            $product = $this->findProduct($id);
            if (!$product) {
                throw new ErrorException(
                    ErrorException::PARAM_INCORRECT,
                    "Product with ID {$id} not found"
                );
            }
            $products[] = $product;
        }
        return $products;
    }
    
    private function calculateTotal(array $products): float {
        return array_sum(array_column($products, 'price'));
    }
    
    private function saveOrder(array $productIds, string $name, string $email, float $total): int {
        // Simulate order creation
        return 12345;
    }
    
    private function findOrder(int $id): ?array {
        // Simulate order lookup
        return [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'total' => 199.98
        ];
    }
    
    private function findProduct(int $id): ?array {
        // Simulate product lookup
        $products = [
            1 => ['price' => 99.99],
            2 => ['price' => 49.99],
        ];
        return $products[$id] ?? null;
    }
    
    private function updateOrderStatus(int $id, OrderStatus $status): void {
        // Simulate status update
    }
}
```

### File Upload Handling

```php
<?php
// src/API/Methods/FileUpload.php
namespace API\Methods;

use MotionDots\Method\AbstractMethod;
use MotionDots\Exception\ErrorException;
use API\Responses\FileUploadResponse;

class FileUpload extends AbstractMethod {
    
    public function uploadImage(array $file): FileUploadResponse {
        // Validate file
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                'Invalid file upload'
            );
        }
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                'Only JPEG, PNG, and GIF images are allowed'
            );
        }
        
        // Validate file size (5MB max)
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                'File size must be less than 5MB'
            );
        }
        
        // Generate unique filename
        $filename = uniqid() . '_' . $file['name'];
        $uploadPath = 'uploads/' . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new ErrorException(
                ErrorException::INTERNAL_ERROR,
                'Failed to save uploaded file'
            );
        }
        
        // Set context
        $this->context->set('uploadedFile', $uploadPath);
        
        return FileUploadResponse::create()
            ->setFilename($filename)
            ->setOriginalName($file['name'])
            ->setSize($file['size'])
            ->setType($file['type'])
            ->setUrl('/uploads/' . $filename);
    }
}
```

## Integration Examples

### Database Integration

```php
<?php
// src/Database/Database.php
namespace API\Database;

class Database {
    private \PDO $pdo;
    
    public function __construct(string $dsn, string $username, string $password) {
        $this->pdo = new \PDO($dsn, $username, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
    
    public function findUser(int $id): ?array {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }
    
    public function createUser(string $name, string $email, string $password): int {
        $stmt = $this->pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
        return $this->pdo->lastInsertId();
    }
}
```

### Using Database in Methods

```php
<?php
// src/API/Methods/DatabaseUsers.php
namespace API\Methods;

use MotionDots\Method\AbstractMethod;
use API\Database\Database;
use API\Responses\UserResponse;
use API\Types\EmailType;
use API\Types\PasswordType;
use MotionDots\Type\PositiveType;

class DatabaseUsers extends AbstractMethod {
    
    private Database $db;
    
    public function __construct() {
        $this->db = new Database(
            'mysql:host=localhost;dbname=myapp',
            'username',
            'password'
        );
    }
    
    public function getUser(PositiveType $id): UserResponse {
        $userId = $id->parse();
        $user = $this->db->findUser($userId);
        
        if (!$user) {
            throw new ErrorException(
                ErrorException::PARAM_INCORRECT,
                "User with ID {$userId} not found"
            );
        }
        
        return UserResponse::create()
            ->setId($userId)
            ->setName($user['name'])
            ->setEmail($user['email']);
    }
    
    public function createUser(
        string $name,
        EmailType $email,
        PasswordType $password
    ): UserResponse {
        $emailValue = $email->parse();
        $passwordValue = $password->parse();
        
        $userId = $this->db->createUser($name, $emailValue, $passwordValue);
        
        return UserResponse::create()
            ->setId($userId)
            ->setName($name)
            ->setEmail($emailValue);
    }
}
```

### TypeScript Generation

```php
<?php
// generate-types.php
require_once 'vendor/autoload.php';

use MotionDots\Process\Processor;
use MotionDots\Schema\Schema;
use MotionDots\Schema\Typescript\Generator;
use API\Methods\Users;
use API\Methods\Products;
use API\Methods\Orders;

// Create schema
$schema = Schema::create()->addMethods([
    new Users(),
    new Products(),
    new Orders(),
]);

// Create processor
$processor = new Processor($schema, '.');

// Generate TypeScript
Generator::create()
    ->setFilesPath('./frontend/src/types/api')
    ->setIsVerbose(true)
    ->excludeSpaces('admin', 'debug')
    ->generate($processor);

echo "TypeScript definitions generated successfully!\n";
```

### Frontend Integration

```typescript
// frontend/src/api/client.ts
import { 
    GetUserMethod, 
    GetUserParams, 
    GetUserResponse,
    CreateUserMethod,
    CreateUserParams,
    CreateUserResponse
} from '../types/api/methods';

class APIClient {
    private baseUrl: string;
    
    constructor(baseUrl: string) {
        this.baseUrl = baseUrl;
    }
    
    async getUser(params: GetUserParams): Promise<GetUserResponse> {
        const response = await fetch(`${this.baseUrl}/api/${GetUserMethod}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(params)
        });
        
        const data = await response.json();
        
        if ('error' in data) {
            throw new Error(data.error.error_message);
        }
        
        return data.response as GetUserResponse;
    }
    
    async createUser(params: CreateUserParams): Promise<CreateUserResponse> {
        const response = await fetch(`${this.baseUrl}/api/${CreateUserMethod}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(params)
        });
        
        const data = await response.json();
        
        if ('error' in data) {
            throw new Error(data.error.error_message);
        }
        
        return data.response as CreateUserResponse;
    }
}

// Usage
const client = new APIClient('https://api.example.com');

// Type-safe API calls
const user = await client.getUser({ id: 123 });
const newUser = await client.createUser({
    name: 'John Doe',
    email: 'john@example.com',
    password: 'SecurePass123'
});
```

## Best Practices Examples

### Error Handling

```php
<?php
// src/API/Methods/RobustUsers.php
namespace API\Methods;

use MotionDots\Method\AbstractMethod;
use MotionDots\Exception\ErrorException;
use API\Responses\UserResponse;
use API\Types\EmailType;
use MotionDots\Type\PositiveType;

class RobustUsers extends AbstractMethod {
    
    public function getUser(PositiveType $id): UserResponse {
        try {
            $userId = $id->parse();
            $user = $this->findUser($userId);
            
            if (!$user) {
                throw new ErrorException(
                    ErrorException::PARAM_INCORRECT,
                    "User with ID {$userId} not found"
                );
            }
            
            return UserResponse::create()
                ->setId($userId)
                ->setName($user['name'])
                ->setEmail($user['email']);
                
        } catch (ErrorException $e) {
            // Re-throw known errors
            throw $e;
        } catch (\Exception $e) {
            // Log unexpected errors
            error_log("Unexpected error in getUser: " . $e->getMessage());
            
            throw new ErrorException(
                ErrorException::INTERNAL_ERROR,
                "An unexpected error occurred"
            );
        }
    }
    
    public function createUser(
        string $name,
        EmailType $email,
        string $password
    ): UserResponse {
        try {
            $emailValue = $email->parse();
            
            // Validate name
            if (empty(trim($name))) {
                throw new ErrorException(
                    ErrorException::PARAM_INCORRECT,
                    "Name cannot be empty"
                );
            }
            
            // Check if user exists
            if ($this->userExists($emailValue)) {
                throw new ErrorException(
                    ErrorException::PARAM_INCORRECT,
                    "User with email {$emailValue} already exists"
                );
            }
            
            // Create user
            $userId = $this->saveUser($name, $emailValue, $password);
            
            return UserResponse::create()
                ->setId($userId)
                ->setName($name)
                ->setEmail($emailValue);
                
        } catch (ErrorException $e) {
            throw $e;
        } catch (\Exception $e) {
            error_log("Unexpected error in createUser: " . $e->getMessage());
            throw new ErrorException(
                ErrorException::INTERNAL_ERROR,
                "Failed to create user"
            );
        }
    }
    
    private function findUser(int $id): ?array {
        // Implementation
        return null;
    }
    
    private function userExists(string $email): bool {
        // Implementation
        return false;
    }
    
    private function saveUser(string $name, string $email, string $password): int {
        // Implementation
        return 1;
    }
}
```

### Logging and Monitoring

```php
<?php
// src/API/Methods/LoggedUsers.php
namespace API\Methods;

use MotionDots\Method\AbstractMethod;
use API\Responses\UserResponse;
use MotionDots\Type\PositiveType;

class LoggedUsers extends AbstractMethod {
    
    public function getUser(PositiveType $id): UserResponse {
        $startTime = microtime(true);
        $userId = $id->parse();
        
        try {
            $user = $this->findUser($userId);
            
            if (!$user) {
                $this->logError("User not found", ['userId' => $userId]);
                throw new ErrorException(
                    ErrorException::PARAM_INCORRECT,
                    "User with ID {$userId} not found"
                );
            }
            
            $response = UserResponse::create()
                ->setId($userId)
                ->setName($user['name'])
                ->setEmail($user['email']);
            
            $this->logSuccess("User retrieved", [
                'userId' => $userId,
                'duration' => microtime(true) - $startTime
            ]);
            
            return $response;
            
        } catch (\Exception $e) {
            $this->logError("Failed to get user", [
                'userId' => $userId,
                'error' => $e->getMessage(),
                'duration' => microtime(true) - $startTime
            ]);
            throw $e;
        }
    }
    
    private function logSuccess(string $message, array $context = []): void {
        $this->log('INFO', $message, $context);
    }
    
    private function logError(string $message, array $context = []): void {
        $this->log('ERROR', $message, $context);
    }
    
    private function log(string $level, string $message, array $context = []): void {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'requestId' => $this->context->get('requestId', 'unknown')
        ];
        
        error_log(json_encode($logData));
    }
    
    private function findUser(int $id): ?array {
        // Implementation
        return null;
    }
}
```

These examples demonstrate the power and flexibility of MotionDots for building robust, type-safe APIs with comprehensive error handling, logging, and frontend integration.
