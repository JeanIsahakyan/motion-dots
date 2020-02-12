<?php

namespace MotionDots\Process;

use MotionDots\Error\InternalErrors;
use MotionDots\Exception\ErrorException;
use MotionDots\Response\AbstractResponse;
use MotionDots\Schema\AbstractSchema;

/**
 * Class Processor
 *
 * @package MotionDots\Process
 *
 * @author Jean Isahakyan jeanisahakyan@gmail.com
 */
class Processor {

    /**
     * @var ContextContainer
     */
    protected $context;

    /**
     * @var AbstractSchema
     */
    protected $schema;

    /**
     * @var string
     */
    protected $method_action_separator;

    /**
     * Processor constructor.
     *
     * @param AbstractSchema $schema
     * @param string $method_and_action_separator
     */
    public function __construct(AbstractSchema $schema, string $method_and_action_separator = '.') {
        $this->schema                  = $schema;
        $this->context                 = new ContextContainer();
        $this->method_action_separator = $method_and_action_separator;
    }

    /**
     * @param string $method_and_action
     *
     * @throws ErrorException
     *
     * @return array
     */
    private function unpackMethodAndAction(string $method_and_action) {
        $result = explode('.', $method_and_action);
        if (count($result) < 2) {
            throw new ErrorException(InternalErrors::METHOD_ACTION_UNDEFINED, "Action can't be null");
        }
        return $result;
    }

    /**
     * @return ContextContainer
     */
    public function getContext(): ContextContainer {
        return $this->context;
    }

    /**
     * @param string $method_and_action
     * @param array $params
     *
     * @throws ErrorException
     *
     * @return AbstractResponse
     */
    private function tryInvokeProcess(string $method_and_action, array $params = []): AbstractResponse {
        [$method_name, $action_name] = $this->unpackMethodAndAction($method_and_action);
        if (!$this->schema->methodExists($method_name)) {
            throw new ErrorException(InternalErrors::METHOD_UNDEFINED, "Undefined method `{$method_name}`");
        }
        $method = $this->schema->getMethod($method_name);
        if (!$method->__actionExists($action_name)) {
            throw new ErrorException(InternalErrors::METHOD_ACTION_UNDEFINED, "Undefined action `{$action_name}` in method `{$method_name}`");
        }
        return ActionProcessor::create($method, $action_name, $this->getContext(), $params)->invoke();
    }


    /**
     * @param array $response
     *
     * @return mixed
     */
    private function buildResponse($response) {
        $return_response = [];
        foreach ($response as $key => $row) {
            if (strpos($key, '__internal_method_errors') !== false) {
                continue;
            }
            if (is_object($row)) {
                if (method_exists($row, 'build')) {
                    $row = $row->build();
                } else {
                    $row = (array)$row;
                }
            }
            if (is_array($row)) {
                $row = $this->buildResponse($row);
            }
            $return_response[$key] = $row;
        }
        return $return_response;
    }

    /**
     * @param string $method_and_action
     * @param array $params
     *
     * @throws ErrorException
     *
     * @return array
     */
    public function invokeProcess(string $method_and_action, array $params = []): array {
        $response = (array)$this->tryInvokeProcess($method_and_action, $params);
        return $this->buildResponse($response);
    }

    /**
     * @return array
     */
    public function getSchemaInfo(): array {
        return (new SchemaInfoProcessor($this->schema))->build();
    }

}
