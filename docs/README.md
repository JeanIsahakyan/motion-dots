# MotionDots Documentation

**MotionDots** is a lightweight PHP framework designed to simplify API development by providing tools for dynamic method invocation, input validation, structured responses, and TypeScript schema generation.

## Table of Contents

- [Overview](#overview)
- [Architecture](#architecture)
- [Quick Start](#quick-start)
- [Core Components](#core-components)
- [Method System](#method-system)
- [Type System](#type-system)
- [Response System](#response-system)
- [TypeScript Generation](#typescript-generation)
- [Error Handling](#error-handling)
- [API Reference](#api-reference)
- [Examples](#examples)
- [Best Practices](#best-practices)

## Overview

MotionDots provides a clean, type-safe way to build APIs with the following key features:

- **Dynamic Method Invocation**: Automatically maps API requests to class methods
- **Type Safety**: Custom type system with validation and parsing
- **Context Sharing**: Shared data across methods and types during request lifecycle
- **Enum Support**: Native PHP 8.1 enum integration
- **Error Handling**: Comprehensive error system with predefined error codes
- **TypeScript Generation**: Automatic generation of TypeScript definitions
- **System Methods**: Built-in methods for schema introspection and utilities

## Architecture

MotionDots follows a layered architecture:

```
┌─────────────────────────────────────┐
│           API Layer                 │
│  (Method Classes, Responses)        │
├─────────────────────────────────────┤
│         Process Layer               │
│  (Processor, MethodProcessor)       │
├─────────────────────────────────────┤
│          Type Layer                 │
│  (Type System, Validation)          │
├─────────────────────────────────────┤
│         Schema Layer                │
│  (Schema Management, Registry)      │
└─────────────────────────────────────┘
```

## Quick Start

### Installation

```bash
composer require jeanisahakyan/motion-dots
```

### Basic Setup

```php
<?php
use MotionDots\Process\Processor;
use MotionDots\Schema\Schema;
use API\Methods\Users;

// Create schema and add methods
$schema = Schema::create()->addMethods([
    new Users(),
]);

// Create processor
$processor = new Processor($schema, '.');

// Handle request
$response = $processor->invokeProcess('users.getUser', $params);
echo json_encode($response);
```

## Requirements

- PHP 8.1 or higher
- JSON extension
- Composer for dependency management

## License

MIT License - see [LICENSE](../LICENSE) file for details.
