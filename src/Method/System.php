<?php
namespace MotionDots\Method;
use MotionDots\Process\ContextContainer;
use MotionDots\Response\ResponseInterface;
use MotionDots\Schema\Schema;
use MotionDots\Type\BuiltinType;
use MotionDots\Type\TypeInterface;

class System extends AbstractMethod {
  private $schema_info;
  private $method_action_separator;

  public function __construct(Schema &$schema_info, string  $method_action_separator) {
    $this->schema_info             = $schema_info;
    $this->method_action_separator = $method_action_separator;
  }

  private function parseType(?\ReflectionType $type): ?array {
    if (!$type) {
      return null;
    }
    $name = $type->getName();
    if ($name === ContextContainer::class) {
      return null;
    }
    if ($type->isBuiltin()) {
      return [
        'name'        => $name,
        'description' => "Type: {$name}",
        'example'     => BuiltinType::EXAMPLES[$name],
        'isRequired'  => !$type->allowsNull(),
      ];
    }
    $class = new \ReflectionClass($name);
    [$interface] = $class->getInterfaceNames();
    if ($interface === TypeInterface::class) {
      return [
        'name'        => $name::NAME,
        'description' => $name::DESCRIPTION,
        'example'     => (new $name)->example(),
        'isRequired'  => !$type->allowsNull(),
      ];
    }
    return null;
  }

  public function getSchema(): array {
    $result = [];
    foreach ($this->schema_info->getMethods() as $method_name => $method) {
      try {
        $reflection = new \ReflectionClass($method);
      } catch (\ReflectionException $exception) {
        continue;
      }
      foreach ($reflection->getMethods() as $action) {
        $action_name = $action->getName();
        if ($action->isPrivate()) {
          continue;
        }
        if (substr($action_name, 0, 2) === '__') {
          continue;
        }
        $params   = [];
        foreach ($action->getParameters() as $parameter) {
          $params[] = [
            'name'       => $parameter->getName(),
            'type'       => $this->parseType($parameter->getType()),
          ];
        }
        $return_type = $action->getReturnType();
        $response = 'unknown';
        if ($return_type) {
          if ($return_type->isBuiltin()) {
            $response = [
              'type'       => $this->parseType($return_type),
            ];
          } else {
            $response_reflection = new \ReflectionClass($return_type->getName());
            $response = [
              ResponseInterface::TYPE_ID_FIELD => [
                [
                  'name' => ResponseInterface::TYPE_ID_FIELD,
                  'type' => 'string',
                ]
              ],
            ];
            foreach ($response_reflection->getProperties() as $property) {
              $response[$property->getName()] = [
                'name'       => $property->getName(),
                'type'       => 'unknown',
              ];
            }
            if ($response_reflection->hasMethod('__construct')) {
              $response_value = $response_reflection->getMethod('__construct');
              foreach ($response_value->getParameters() as $response_field) {
                $response_field_name = $response_field->getName();
                if (array_key_exists($response_field_name, $response)) {
                  $response[$response_field_name]['type'] = $this->parseType($response_field->getType());
                }
              }
            }
            $response = array_values($response);
          }
        }
        $method =  [
          'name'     => "{$method_name}{$this->method_action_separator}{$action_name}",
        ];
        if ($params) {
          $method['params'] = $params;
        }
        if ($response) {
          $method['response'] = $response;
        }
        if (!array_key_exists($method_name, $result)) {
          $result[$method_name] = [
            'name'    => $method_name,
            'methods' => [],
          ];
        }

        $result[$method_name]['methods'][] = $method;
      }
    }
    return array_values($result);
  }

  public function serverTime(): int {
    return time();
  }

  public function increment(int $counter): int {
    return $counter + 1;
  }
}
