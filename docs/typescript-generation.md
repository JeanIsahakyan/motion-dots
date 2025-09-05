# TypeScript Generation Documentation

## Overview

The TypeScript Generation system in MotionDots automatically creates TypeScript definitions for your API, enabling type-safe frontend development. It analyzes your PHP classes, methods, and response types to generate comprehensive TypeScript interfaces, enums, and method definitions.

## Generator Class

**File**: `src/Schema/Typescript/Generator.php`  
**Namespace**: `MotionDots\Schema\Typescript`

The main generator class that orchestrates the TypeScript file creation process.

### Properties

#### `$excluded_spaces`

```php
private array $excluded_spaces = ['system'];
```

Array of method spaces to exclude from generation (default: excludes 'system').

#### `$is_verbose`

```php
private bool $is_verbose = true;
```

Whether to output verbose generation messages (default: true).

#### `$files_path`

```php
private string $files_path = './api-schema';
```

Output directory for generated TypeScript files (default: './api-schema').

### Static Methods

#### `create(): self`

Factory method to create a new Generator instance.

**Returns:** New Generator instance

**Example:**
```php
$generator = Generator::create();
```

### Instance Methods

#### `excludeSpaces(string ...$space_names): self`

Adds method spaces to exclude from generation.

**Parameters:**
- `$space_names` (string): Variable number of space names to exclude

**Returns:** Self for method chaining

**Example:**
```php
$generator->excludeSpaces('admin', 'internal', 'debug');
```

#### `setIsVerbose(bool $is_verbose): self`

Sets the verbose output mode.

**Parameters:**
- `$is_verbose` (bool): Whether to output verbose messages

**Returns:** Self for method chaining

**Example:**
```php
$generator->setIsVerbose(false);
```

#### `setFilesPath(string $files_path): self`

Sets the output directory for generated files.

**Parameters:**
- `$files_path` (string): Directory path for output files

**Returns:** Self for method chaining

**Example:**
```php
$generator->setFilesPath('./src/types/api');
```

#### `generate(Processor $processor): void`

Generates TypeScript files from the processor's schema.

**Parameters:**
- `$processor` (Processor): Processor instance with registered methods

**Example:**
```php
$generator->generate($processor);
```

## Type Nodes

### MethodNode

**File**: `src/Schema/Typescript/Nodes/MethodNode.php`

Represents a TypeScript method definition with parameters and response types.

#### Constructor

```php
public function __construct(array $schema, string $separator)
```

**Parameters:**
- `$schema` (array): Method schema from system.getSchema
- `$separator` (string): Method separator (e.g., '.')

#### Methods

#### `toString(): string`

Generates TypeScript method definition.

**Returns:** TypeScript code for method definition

**Example Output:**
```typescript
export const GetUserMethod = 'users.getUser';
export interface GetUserParams {
  id: number;
}
export type GetUserResponse = UserResponse;
```

#### `getObjects(): ObjectNode[]`

Returns all object types used in the method.

**Returns:** Array of ObjectNode instances

#### `getEnums(): EnumNode[]`

Returns all enum types used in the method.

**Returns:** Array of EnumNode instances

### ObjectNode

**File**: `src/Schema/Typescript/Nodes/ObjectNode.php`

Represents a TypeScript interface for response objects.

#### Constructor

```php
public function __construct(array $schema)
```

**Parameters:**
- `$schema` (array): Object schema from system.getSchema

#### Methods

#### `toString(): string`

Generates TypeScript interface definition.

**Returns:** TypeScript interface code

**Example Output:**
```typescript
export type UserResponse = {
  id: number;
  name: string;
  email: string;
  status: UserStatus;
};
```

#### `getTypeName(): string`

Returns the TypeScript type name.

**Returns:** Type name string

#### `getInnerObjects(): ObjectNode[]`

Returns nested object types.

**Returns:** Array of nested ObjectNode instances

#### `getInnerEnums(): EnumNode[]`

Returns nested enum types.

**Returns:** Array of nested EnumNode instances

### EnumNode

**File**: `src/Schema/Typescript/Nodes/EnumNode.php`

Represents a TypeScript enum definition.

#### Constructor

```php
public function __construct(array $schema)
```

**Parameters:**
- `$schema` (array): Enum schema from system.getSchema

#### Methods

#### `toString(): string`

Generates TypeScript enum definition.

**Returns:** TypeScript enum code

**Example Output:**
```typescript
export enum UserStatus {
  ACTIVE = 'active',
  INACTIVE = 'inactive',
  BANNED = 'banned',
}
```

#### `getTypeName(): string`

Returns the TypeScript enum name.

**Returns:** Enum name string

### PrimitiveNode

**File**: `src/Schema/Typescript/Nodes/PrimitiveNode.php`

Represents primitive TypeScript types.

#### Constructor

```php
public function __construct(array $schema)
```

**Parameters:**
- `$schema` (array): Primitive type schema

#### Methods

#### `toString(): string`

Returns the TypeScript primitive type name.

**Returns:** Type name string (e.g., 'string', 'number', 'boolean')

## Type Mapper

**File**: `src/Schema/Typescript/TypeMapper.php`  
**Namespace**: `MotionDots\Schema\Typescript`

Maps PHP types to TypeScript types and creates appropriate node instances.

### Static Methods

#### `map(array $schema): TypeNodeInterface`

Creates the appropriate TypeNode based on schema type.

**Parameters:**
- `$schema` (array): Type schema

**Returns:** TypeNodeInterface implementation

**Example:**
```php
$node = TypeMapper::map($schema);
```

#### `mapName(string $type): string`

Maps PHP type names to TypeScript type names.

**Parameters:**
- `$type` (string): PHP type name

**Returns:** TypeScript type name

**Mapping:**
- `int` → `number`
- `float` → `number`
- `bool` → `boolean`
- `string` → `string`
- `null` → `null`
- `array` → `any[]`
- `mixed` → `any`
- `object` → `object`

## Repositories

### MethodsRepository

**File**: `src/Schema/Typescript/Repositories/MethodsRepository.php`

Manages method definitions for TypeScript generation.

#### Static Methods

#### `add(string $space, array $methods): void`

Adds methods for a specific space.

**Parameters:**
- `$space` (string): Method space name
- `$methods` (array): Array of MethodNode instances

#### `getSpacesMethods(): array`

Returns all method spaces.

**Returns:** Array of space_name => methods

#### `getExports(): string`

Generates export statements for all method spaces.

**Returns:** TypeScript export code

### ObjectsRepository

**File**: `src/Schema/Typescript/Repositories/ObjectsRepository.php`

Manages object type definitions for TypeScript generation.

#### Static Methods

#### `add(ObjectNode $object): void`

Adds an object type definition.

**Parameters:**
- `$object` (ObjectNode): Object node instance

#### `getAll(): array`

Returns all object definitions.

**Returns:** Array of ObjectNode instances

#### `getExports(array $objects): string`

Generates export statements for objects.

**Parameters:**
- `$objects` (array): Array of ObjectNode instances

**Returns:** TypeScript export code

#### `getImports(string $relative_path, array $objects): string`

Generates import statements for objects.

**Parameters:**
- `$relative_path` (string): Relative path for imports
- `$objects` (array): Array of ObjectNode instances

**Returns:** TypeScript import code

### EnumsRepository

**File**: `src/Schema/Typescript/Repositories/EnumsRepository.php`

Manages enum definitions for TypeScript generation.

#### Static Methods

#### `add(EnumNode $enum): void`

Adds an enum definition.

**Parameters:**
- `$enum` (EnumNode): Enum node instance

#### `getAll(): array`

Returns all enum definitions.

**Returns:** Array of EnumNode instances

#### `getExports(array $enums): string`

Generates export statements for enums.

**Parameters:**
- `$enums` (array): Array of EnumNode instances

**Returns:** TypeScript export code

#### `getImports(string $relative_path, array $enums): string`

Generates import statements for enums.

**Parameters:**
- `$relative_path` (string): Relative path for imports
- `$enums` (array): Array of EnumNode instances

**Returns:** TypeScript import code

## Generated File Structure

The generator creates the following file structure:

```
api-schema/
├── methods/
│   ├── users.d.ts
│   ├── products.d.ts
│   └── index.d.ts
├── responses/
│   ├── UserResponse.d.ts
│   ├── ProductResponse.d.ts
│   └── index.d.ts
├── enums/
│   ├── UserStatus.d.ts
│   ├── ProductCategory.d.ts
│   └── index.d.ts
```

### Methods Files

Each method space gets its own file with method definitions:

```typescript
// methods/users.d.ts
import { UserResponse } from '../responses';
import { UserStatus } from '../enums';

export const GetUserMethod = 'users.getUser';
export interface GetUserParams {
  id: number;
}
export type GetUserResponse = UserResponse;

export const CreateUserMethod = 'users.createUser';
export interface CreateUserParams {
  name: string;
  email: string;
  status: UserStatus;
}
export type CreateUserResponse = UserResponse;
```

### Response Files

Each response class gets its own file:

```typescript
// responses/UserResponse.d.ts
import { UserStatus } from '../enums';

export type UserResponse = {
  id: number;
  name: string;
  email: string;
  status: UserStatus;
  avatar?: string;
  createdAt?: string;
  updatedAt?: string;
};
```

### Enum Files

Each enum gets its own file:

```typescript
// enums/UserStatus.d.ts
export enum UserStatus {
  ACTIVE = 'active',
  INACTIVE = 'inactive',
  BANNED = 'banned',
}
```

### Index Files

Each directory has an index file for easy imports:

```typescript
// methods/index.d.ts
export * from './users';
export * from './products';

// responses/index.d.ts
export * from './UserResponse';
export * from './ProductResponse';

// enums/index.d.ts
export * from './UserStatus';
export * from './ProductCategory';
```

## Usage Examples

### Basic Generation

```php
<?php
use MotionDots\Process\Processor;
use MotionDots\Schema\Schema;
use MotionDots\Schema\Typescript\Generator;
use API\Methods\Users;
use API\Methods\Products;

// Create schema
$schema = Schema::create()->addMethods([
    new Users(),
    new Products(),
]);

// Create processor
$processor = new Processor($schema, '.');

// Generate TypeScript
Generator::create()
    ->setFilesPath('./src/types/api')
    ->setIsVerbose(true)
    ->excludeSpaces('admin', 'debug')
    ->generate($processor);
```

### Advanced Configuration

```php
<?php
// Create generator with custom settings
$generator = Generator::create()
    ->setFilesPath('./frontend/src/types/api')
    ->setIsVerbose(false)
    ->excludeSpaces('admin', 'internal', 'debug', 'system');

// Generate TypeScript files
$generator->generate($processor);
```

### Integration with Build Process

```php
<?php
// build-types.php
require_once 'vendor/autoload.php';

use MotionDots\Process\Processor;
use MotionDots\Schema\Schema;
use MotionDots\Schema\Typescript\Generator;

// Load all method classes
$methods = [
    new API\Methods\Users(),
    new API\Methods\Products(),
    new API\Methods\Orders(),
    new API\Methods\Categories(),
];

// Create schema and processor
$schema = Schema::create()->addMethods($methods);
$processor = new Processor($schema, '.');

// Generate TypeScript
Generator::create()
    ->setFilesPath('./frontend/src/types/api')
    ->setIsVerbose(true)
    ->excludeSpaces('admin', 'debug')
    ->generate($processor);

echo "TypeScript definitions generated successfully!\n";
```

## Frontend Integration

### Using Generated Types

```typescript
// frontend/src/api/users.ts
import { 
    GetUserMethod, 
    GetUserParams, 
    GetUserResponse,
    CreateUserMethod,
    CreateUserParams,
    CreateUserResponse
} from '../types/api/methods';
import { UserResponse } from '../types/api/responses';
import { UserStatus } from '../types/api/enums';

class UserAPI {
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
        return data.response as GetUserResponse;
    }
    
    async createUser(params: CreateUserParams): Promise<CreateUserResponse> {
        const response = await fetch(`${this.baseUrl}/api/${CreateUserMethod}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(params)
        });
        
        const data = await response.json();
        return data.response as CreateUserResponse;
    }
}
```

### Type-Safe API Client

```typescript
// frontend/src/api/client.ts
import { UserResponse, ProductResponse } from '../types/api/responses';
import { UserStatus, ProductCategory } from '../types/api/enums';

interface APIResponse<T> {
    response: T;
}

interface APIError {
    error: {
        error_code: number;
        error_message: string;
    };
}

class APIClient {
    private baseUrl: string;
    
    constructor(baseUrl: string) {
        this.baseUrl = baseUrl;
    }
    
    async call<T>(method: string, params: any): Promise<T> {
        const response = await fetch(`${this.baseUrl}/api/${method}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(params)
        });
        
        const data = await response.json();
        
        if ('error' in data) {
            throw new Error(data.error.error_message);
        }
        
        return data.response as T;
    }
}

// Usage
const client = new APIClient('https://api.example.com');

// Type-safe method calls
const user = await client.call<UserResponse>('users.getUser', { id: 123 });
const products = await client.call<ProductResponse[]>('products.getMany', { ids: [1, 2, 3] });
```

## Best Practices

### Naming Conventions

1. **Response Classes**: Use descriptive names ending with 'Response'
2. **Enum Classes**: Use descriptive names for enum types
3. **Method Classes**: Use descriptive names for method groups
4. **TypeScript Types**: Generated types follow the same naming conventions

### File Organization

1. **Separate Directories**: Keep methods, responses, and enums in separate directories
2. **Index Files**: Use index files for easy imports
3. **Consistent Imports**: Use relative imports for better organization
4. **Version Control**: Include generated files in version control for consistency

### Type Safety

1. **Strict Types**: Use strict TypeScript configuration
2. **Interface Validation**: Validate API responses against generated interfaces
3. **Error Handling**: Use proper error handling with typed error responses
4. **Documentation**: Document complex type relationships

### Build Integration

1. **Automated Generation**: Integrate generation into your build process
2. **Version Control**: Track generated files for consistency
3. **CI/CD**: Include type generation in continuous integration
4. **Validation**: Validate generated types against API schema

## Troubleshooting

### Common Issues

1. **Missing Types**: Ensure all response classes are properly registered
2. **Circular Dependencies**: Avoid circular references in response objects
3. **Enum Conflicts**: Ensure enum names are unique across the API
4. **Type Mismatches**: Verify PHP types map correctly to TypeScript types

### Debugging

1. **Verbose Output**: Enable verbose mode to see generation progress
2. **Schema Inspection**: Use `system.getSchema` to inspect the API schema
3. **File Validation**: Check generated files for syntax errors
4. **Type Validation**: Validate generated types with TypeScript compiler

The TypeScript generation system provides a powerful way to maintain type safety between your PHP API and TypeScript frontend, ensuring consistency and reducing runtime errors.
