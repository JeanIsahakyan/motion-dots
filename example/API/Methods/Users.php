<?php
namespace API\Methods;

use API\Responses\Common\ListResponse;
use API\Responses\Users\UserResponse;
use API\Types\CodeType;
use MotionDots\Exception\ErrorException;
use MotionDots\Method\AbstractMethod;
use MotionDots\Type\PositiveListType;
use MotionDots\Type\PositiveType;

class Users extends AbstractMethod {

  /**
   *  Private methods is not shown in schema and can't be called from outside
   */
  private function generateName(int $id): string {
    static $last_names = [
      "Abbott",
      "Abernathy",
      "Abshire",
    ];
    static $female_first_names = [
      "Mary",
      "Patricia",
      "Linda",
    ];
    static $male_first_names = [
      "James",
      "John",
      "Robert",
    ];
    $index = $id % count($last_names);
    if ($id % 2 === 1) {
      $first_name = $female_first_names[$index];
    } else {
      $first_name = $male_first_names[$index];
    }
    return "{$first_name} {$last_names[$index]}";
  }

  public function getMany(PositiveListType $user_ids): ListResponse {
    $user_ids = $user_ids->parse(); // int[]
    $items = [];
    foreach ($user_ids as $user_id) {
      $items[] = UserResponse::create()
        ->setId($user_id)
        ->setName($this->generateName($user_id));
    }
    return ListResponse::create()
      ->setItems($items)
      ->setCount(count($user_ids));
  }

  public function getOne(PositiveType $user_id): UserResponse {
    $user_id = $user_id->parse(); // int
    if ($user_id === 1) {
      throw new ErrorException(ErrorException::PARAM_INCORRECT, 'Error for user 1');
    }
    return UserResponse::create()
      ->setId($user_id)
      ->setName($this->generateName($user_id));
  }

  public function verifyCode(CodeType $code): int {
    $code = $code->parse();
    if ($code) {
      // 6 digit int code
    }
    return $code;
  }

  public function notRequiredFields(?int $user_id, ?string $name, ?CodeType $code): string {
    $code = $code ? $code->parse() : null; // trying to parse if param is in request
    return "name: '{$name}', user_id: '{$user_id}', code: '{$code}'";
  }
}
