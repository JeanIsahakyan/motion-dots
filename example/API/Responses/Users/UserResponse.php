<?php
namespace API\Responses\Users;

use MotionDots\Response\AbstractResponse;

class UserResponse extends AbstractResponse {
  public $id = 0;
  public $name = '';

  /**
   * @return int
   */
  public function getId(): int {
    return $this->id;
  }

  /**
   * @param int $id
   * @return UserResponse
   */
  public function setId(int $id): UserResponse {
    $this->id = $id;
    return $this;
  }

  /**
   * @return string
   */
  public function getName(): string {
    return $this->name;
  }

  /**
   * @param string $name
   * @return UserResponse
   */
  public function setName(string $name): UserResponse {
    $this->name = $name;
    return $this;
  }


}
