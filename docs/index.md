# MotionDots Documentation Index

Welcome to the complete documentation for MotionDots, a lightweight PHP framework for building robust APIs with dynamic method invocation, input validation, structured responses, and TypeScript generation.

## 📚 Documentation Structure

### Core Documentation

1. **[README](README.md)** - Main overview and quick start guide
2. **[Architecture](architecture.md)** - Detailed system architecture and design patterns
3. **[Core Classes](core-classes.md)** - Documentation for Processor, Schema, Context, and other core components

### System Components

4. **[Method System](method-system.md)** - AbstractMethod, MethodInterface, and system methods
5. **[Type System](type-system.md)** - Custom types, validation, and built-in type handlers
6. **[Response System](response-system.md)** - AbstractResponse, response formatting, and serialization
7. **[TypeScript Generation](typescript-generation.md)** - Automatic TypeScript definition generation

### Practical Guides

8. **[Examples](examples.md)** - Comprehensive examples, real-world scenarios, and best practices
9. **[API Reference](api-reference.md)** - Complete API reference with all classes, methods, and constants

## 🚀 Quick Navigation

### For New Users
- Start with [README](README.md) for overview and installation
- Follow the [Architecture](architecture.md) guide to understand the system
- Check [Examples](examples.md) for practical implementation

### For Developers
- Use [API Reference](api-reference.md) for detailed method signatures
- Reference [Core Classes](core-classes.md) for implementation details
- See [TypeScript Generation](typescript-generation.md) for frontend integration

### For System Design
- Review [Architecture](architecture.md) for system design patterns
- Study [Method System](method-system.md) for API endpoint design
- Examine [Type System](type-system.md) for validation strategies

## 📋 Key Features Covered

### ✅ Dynamic Method Invocation
- Automatic API request mapping to class methods
- Magic method handling and reflection
- Method registration and discovery

### ✅ Type Safety & Validation
- Custom type system with validation
- Built-in types for common use cases
- PHP 8.1 enum support
- Parameter parsing and conversion

### ✅ Structured Responses
- Consistent response formatting
- Automatic JSON serialization
- Type identification for frontend consumption
- Null value filtering

### ✅ Context Management
- Shared data across methods and types
- Request lifecycle data persistence
- Context injection and access

### ✅ Error Handling
- Comprehensive error system
- Predefined error codes
- Structured error responses
- Exception handling patterns

### ✅ TypeScript Generation
- Automatic TypeScript definition generation
- Method, response, and enum type definitions
- Frontend integration support
- Organized file structure

### ✅ System Methods
- Built-in introspection capabilities
- Schema discovery and inspection
- Utility methods for common operations

## 🛠️ Implementation Patterns

### Basic API Setup
```php
$schema = Schema::create()->addMethods([new Users()]);
$processor = new Processor($schema, '.');
$response = $processor->invokeProcess('users.getUser', $params);
```

### Custom Type Definition
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

### Response Class
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

### TypeScript Generation
```php
Generator::create()
    ->setFilesPath('./frontend/src/types/api')
    ->generate($processor);
```

## 📖 Documentation Standards

All documentation follows these standards:

- **Comprehensive Coverage**: Every class, method, and property is documented
- **Code Examples**: Practical examples for every concept
- **Error Handling**: Complete error code reference and handling patterns
- **Best Practices**: Recommended patterns and anti-patterns
- **Real-World Scenarios**: Practical implementation examples
- **Integration Guides**: Frontend and database integration examples

## 🔍 Finding Information

### By Component
- **Core System**: [Core Classes](core-classes.md), [Architecture](architecture.md)
- **API Development**: [Method System](method-system.md), [Examples](examples.md)
- **Type Safety**: [Type System](type-system.md), [API Reference](api-reference.md)
- **Frontend Integration**: [TypeScript Generation](typescript-generation.md), [Examples](examples.md)

### By Use Case
- **Building APIs**: [Method System](method-system.md), [Response System](response-system.md)
- **Validation**: [Type System](type-system.md), [Examples](examples.md)
- **Frontend Development**: [TypeScript Generation](typescript-generation.md)
- **Error Handling**: [API Reference](api-reference.md), [Examples](examples.md)

### By Experience Level
- **Beginners**: [README](README.md), [Examples](examples.md)
- **Intermediate**: [Architecture](architecture.md), [Method System](method-system.md)
- **Advanced**: [API Reference](api-reference.md), [TypeScript Generation](typescript-generation.md)

## 🤝 Contributing to Documentation

This documentation is designed to be:
- **Comprehensive**: Covering all aspects of the framework
- **Practical**: With real-world examples and use cases
- **Maintainable**: Easy to update as the framework evolves
- **Accessible**: Clear for developers of all skill levels

## 📞 Support

For questions about the framework or documentation:
- Review the [Examples](examples.md) for common patterns
- Check the [API Reference](api-reference.md) for specific method details
- Study the [Architecture](architecture.md) for system understanding

---

**MotionDots** - Building robust APIs with PHP 8.1+ features, type safety, and automatic TypeScript generation.
