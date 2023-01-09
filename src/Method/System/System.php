<?php
namespace MotionDots\Method\System;

use MotionDots\Method\AbstractMethod;
use MotionDots\Method\System\Response\SystemMethodParamResponse;
use MotionDots\Method\System\Response\SystemMethodsResponse;
use MotionDots\Method\System\Response\SystemMethodResponse;
use MotionDots\Response\ResponseInterface;
use MotionDots\Schema\Schema;
use MotionDots\Type\TypeInterface;
use UnitEnum;

class System extends AbstractMethod {
  private $schema_info;
  private $separator;

  public function __construct(Schema &$schema_info, string  $separator) {
    $this->schema_info             = &$schema_info;
    $this->separator = $separator;
  }

  private function getName(string $input): string {
    $input = explode('\\', $input);
    $input = end($input);
    $input = substr($input, 0, strlen($input) - 4);
    $input = preg_split('/(^[^A-Z]+|[A-Z][^A-Z]+)/', $input, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    return strtolower(implode('_', $input));
  }

  private function getTypeName(string $name, SystemMethodParamResponse $row): SystemMethodParamResponse {
    if (enum_exists($name)) {
      $row->setType('enum');
      /**
       * @var UnitEnum $name
       */
      $enum = $name::cases();
      $row->setEnum(array_map(fn($case) => $case->value, $enum))
        ->setEnumNames(array_map(fn($case) => $case->name, $enum));
      return $row;
    }
    $class = new \ReflectionClass($name);
    [$interface] = $class->getInterfaceNames();
    if ($interface === ResponseInterface::class) {
      $uses = $this->getUses($class->getFileName());
      $properties = [
        ResponseInterface::TYPE_ID_FIELD => SystemMethodParamResponse::create()
          ->setName(ResponseInterface::TYPE_ID_FIELD)
          ->setType('string'),
      ];
      foreach ($class->getProperties() as $property) {
        $phpdoc = $this->parsePhpdoc($property);
        $properties[$property->getName()] = $this->prepareResponse($property->getName(), $property->getType(), $uses, $phpdoc);
      }
      $row->setType('object');
      $row->setProperties(array_values($properties));
    }
    if ($interface === TypeInterface::class) {
      return $row->setType($this->getName($name));
    }
    return $row;
  }


  private function parsePhpdoc($method) : array {
    $doc = $method->getDocComment();
    $lines = array_map(function($line){
      return trim($line, " *");
    }, explode("\n", $doc));
    $lines = array_filter($lines, fn($line) => str_starts_with($line, '@'));
    $args = [];
    foreach ($lines as $line) {
      [$param, $value] = explode(' ', $line, 2);
      if (($param === '@param' || $param === '@var') && preg_match('/([a-zA-Z0-9_\[\]\|\?]+) \$([a-zA-Z0-9_]+)/i', $value, $matches)) {
        [,$param_type, $param_name] = $matches;
        $union = str_contains($param_type, '|');
        $array = str_contains($param_type, '[]');
        if (!$union && $array) {
          $param_type = str_replace('[]', '', $param_type);
        }
        $param = '@param';
        $args[$param][$param_name] = [
          'array'  => $array,
          'union'  => $union,
          'type'   => $param_type,
        ];
        continue;
      }

      $args[$param][] = $value;
    }
    return $args['@param'] ?? [];
  }

  private function getUses(string $name): array {
    if (!preg_match_all('/use (.+)\;/i', file_get_contents($name), $matches)) {
      return [];
    }
    [, $uses] = $matches;
    return $uses;
  }


  public function getSchema(): array {
    $result = [];
    foreach ($this->schema_info->getMethods() as $method_name => $method) {
      try {
        $reflection = new \ReflectionClass($method);
      } catch (\ReflectionException $exception) {
        continue;
      }
      $row = SystemMethodsResponse::create()
      ->setName($method_name);
      $uses = $this->getUses($reflection->getFileName());
      $methods = [];
      foreach ($reflection->getMethods() as $action) {
        if ($action->isPrivate()) {
          continue;
        }
        $action_name = $action->getName();
        if (str_starts_with($action_name, '__')) {
          continue;
        }
        $phpdoc = $this->parsePhpdoc($action);
        $params   = [];
        foreach ($action->getParameters() as $parameter) {
          $params[] = $this->prepareResponse($parameter->getName(), $parameter->getType(), $uses, $phpdoc);
        }
        $return_type = $action->getReturnType();
        $response = $this->prepareResponse(null, $return_type, $uses, $phpdoc);
        $method = SystemMethodResponse::create()
        ->setName("{$method_name}{$this->separator}{$action_name}");
        if ($params) {
          $method->setParams($params);
        }
        if ($response) {
          $method->setResponse($response);
        }
        $methods[] = $method;
      }
      $row->setMethods($methods);
      $result[] = $row;
    }
    return $result;
  }

  private function prepareResponse(?string $name, $return_type, $uses, $phpdoc) {
    $row = SystemMethodParamResponse::create()
      ->setName($name);
    if (!$return_type) {
      return $row;
    }
    $row->setRequired(!$return_type->allowsNull());
    if ($return_type->isBuiltin()) {
      $row->setType($return_type->getName());
      if ($row->getType() === 'array' && $phpdoc[$row->getName()]) {
        $info = $phpdoc[$row->getName()];
        $use = array_filter($uses, fn ($use) => str_ends_with($use, $info['type']));
        if ($use) {
          [$use] = array_values($use);
          $info['type'] = $use;
        }
        if (enum_exists($info['type'])) {
          /**
           * @var UnitEnum $enum
           */
          $enum = $info['type'];
          $enum = $enum::cases();
          $row->setItems(SystemMethodParamResponse::create()
          ->setType('enum')
          ->setEnum(array_map(fn($case) => $case->value, $enum))
          ->setEnumNames(array_map(fn($case) => $case->name, $enum)));
          return $row;
        }
        $row->setItems($this->getTypeName($info['type'], SystemMethodParamResponse::create()));
      }
      return $row;
    }
    if ($return_type->getName()) {
      return $this->getTypeName($return_type->getName(), $row);
    }
    return $row;
  }

  public function serverTime(): int {
    return time();
  }

  public function increment(int $counter): int {
    return $counter + 1;
  }
}
