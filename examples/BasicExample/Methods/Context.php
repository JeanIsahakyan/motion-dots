<?php
use MotionDots\Method\AbstractMethod;
use MotionDots\Response\AbstractResponse;
use MotionDots\Exception\ErrorException;

require_once './Responses/ContextItemResponse.php';

class Context extends AbstractMethod {

    public function getContextData(\MotionDots\Type\ContextType $context): AbstractResponse {
        $context_fields = $context->build()->getAll();
        $items = [];
        foreach ($context_fields as $context_field => $context_field_value) {
            $items[] = new ContextItemResponse($context_field, $context_field_value);
        }
        return new ItemsResponse($items, count($items));
    }
}