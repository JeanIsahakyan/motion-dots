<?php
namespace MotionDots\Type;
/**
 * Class IntListType
 *
 * @package MotionDots\Type
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
class IntListType extends AbstractType {
  public const NAME = 'int_list';
  public const DESCRIPTION = '';

  public function example(): string {
   return '-1,0,1,2,3';
  }

  public function parse(): array {
    return array_map(function($list_id) {
      return intval(trim($list_id));
    }, explode(',', $this->field));
  }
}
