<?php

namespace MotionDots\Process;

use MotionDots\Schema\AbstractSchema;
use MotionDots\Type\ContextType;
use ReflectionObject;

/**
 * Class SchemaInfoProcessor
 *
 * @package MotionDots\Process
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
class SchemaInfoProcessor {
    protected $schema_info = [];

    /**
     * SchemaInfoProcessor constructor.
     *
     * @param AbstractSchema $schema
     */
    public function __construct(AbstractSchema &$schema) {
        $result = [];
        foreach ($schema->getMethods() as $method_name => $method) {
            $actions    = [];
            try {
                $reflection = new \ReflectionClass($method);
            } catch (\ReflectionException $exception) {
                continue;
            }
            foreach ($reflection->getMethods() as $action) {
                $action_name = $action->getName();
                if (substr($action_name, 0, 2) === '__') {
                    continue;
                }
                $params   = [];
                foreach ($action->getParameters() as $parameter) {
                    if ($parameter->getType()->getName() === ContextType::class) {
                        continue;
                    }
                    if ($parameter->getType()->isBuiltin()) {
                        $type = $parameter->getType()->getName();
                    } elseif (class_exists($parameter->getType()->getName())) {
                        $type = $parameter->getType()->getName();
                        $type_params = new ReflectionObject(new $type);
                        $fields = [];
                        foreach ($type_params->getProperties() as $property) {
                            $fields[] = [
                              'name' => $property->getName(),
                            ];
                        }
                        $type = [
                            'name'    => $type,
                            'fields'  => $fields,
                        ];
                    }
                    $params[] = [
                        'name'       => $parameter->getName(),
                        'type'       => $type,
                        'isRequired' => !$parameter->getType()->allowsNull(),
                    ];
                }
                $response = [];
                $errors   = [];
                $actions[] = [
                    'name'   => $action_name,
                    'params'   => $params,
                    'response' => $response,
                    'errors'   => $errors,
                ];
            }

            $result[] = [
                'name'    => $method_name,
                'actions' => $actions,
            ];
        }
        $this->schema_info['methods'] = $result;
    }

    /**
     * @return array
     */
    public function build(): array {
        return $this->schema_info;
    }
}