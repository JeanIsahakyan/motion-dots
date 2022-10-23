<?php
namespace API\Responses\Common;

use MotionDots\Response\AbstractResponse;

class ListResponse extends AbstractResponse {
  public $count = 0;
  public $items = [];

  /**
   * @return int
   */
  public function getCount(): int {
    return $this->count;
  }

  /**
   * @param int $count
   * @return ListResponse
   */
  public function setCount(int $count): ListResponse {
    $this->count = $count;
    return $this;
  }

  /**
   * @return array
   */
  public function getItems(): array {
    return $this->items;
  }

  /**
   * @param array $items
   * @return ListResponse
   */
  public function setItems(array $items): ListResponse {
    $this->items = $items;
    return $this;
  }


}
