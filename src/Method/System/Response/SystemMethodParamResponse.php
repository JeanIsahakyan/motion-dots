<?php
namespace MotionDots\Method\System\Response;

use MotionDots\Response\AbstractResponse;

class SystemMethodParamResponse extends AbstractResponse {
  public ?string $name;
  public string $type = 'mixed';
  public bool $required = false;
  /**
   * @var SystemMethodParamResponse[]|null|SystemMethodParamResponse
   */
  public $items;
  /**
   * @var SystemMethodParamResponse[]|null
   */
  public ?array $properties;


  public ?array $enum;
  public ?array $enum_names;

  /**
   * @return string|null
   */
  public function getName(): ?string {
    return $this->name;
  }

  /**
   * @param string|null $name
   * @return SystemMethodParamResponse
   */
  public function setName(?string $name): SystemMethodParamResponse {
    $this->name = $name;
    return $this;
  }

  /**
   * @return string
   */
  public function getType(): string {
    return $this->type;
  }

  /**
   * @param string $type
   * @return SystemMethodParamResponse
   */
  public function setType(string $type): SystemMethodParamResponse {
    $this->type = $type;
    return $this;
  }

  /**
   * @return bool
   */
  public function isRequired(): bool {
    return $this->required;
  }

  /**
   * @param bool $required
   * @return SystemMethodParamResponse
   */
  public function setRequired(bool $required): SystemMethodParamResponse {
    $this->required = $required;
    return $this;
  }

  /**
   * @return array|null
   */
  public function getItems(): ?array {
    return $this->items;
  }

  /**
   * @param mixed $items
   * @return SystemMethodParamResponse
   */
  public function setItems($items): SystemMethodParamResponse {
    $this->items = $items;
    return $this;
  }

  /**
   * @return array|null
   */
  public function getProperties(): ?array {
    return $this->properties;
  }

  /**
   * @param array|null $properties
   * @return SystemMethodParamResponse
   */
  public function setProperties(?array $properties): SystemMethodParamResponse {
    $this->properties = $properties;
    return $this;
  }

  /**
   * @return array|null
   */
  public function getEnum(): ?array {
    return $this->enum;
  }

  /**
   * @param array|null $enum
   * @return SystemMethodParamResponse
   */
  public function setEnum(?array $enum): SystemMethodParamResponse {
    $this->enum = $enum;
    return $this;
  }

  /**
   * @return array|null
   */
  public function getEnumNames(): ?array {
    return $this->enum_names;
  }

  /**
   * @param array|null $enum_names
   * @return SystemMethodParamResponse
   */
  public function setEnumNames(?array $enum_names): SystemMethodParamResponse {
    $this->enum_names = $enum_names;
    return $this;
  }
}
