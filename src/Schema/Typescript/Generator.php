<?php

namespace MotionDots\Schema\Typescript;

use MotionDots\Process\Processor;
use MotionDots\Schema\Typescript\Nodes\MethodsNode;

class Generator {

  /** @var string[] */
  private array $excluded_spaces = [
    'system',
  ];

  public static function create(): self {
    return new self();
  }

  public function generate(Processor $processor, string $files_folder): void {
    $files_folder = rtrim($files_folder, '/');

    $response = $processor->execute('system.getSchema');
    $schema = $response['response'];
    $separator = $processor->getSeparator();

    foreach ($schema as $space_schema) {
      $space_name = (string)$space_schema['name'];
      if ($this->isExcludedSpace($space_name)) {
        continue;
      }
      $space = new MethodsNode($space_schema, $separator);
      FileWriter::write("{$files_folder}/{$space_name}.d.ts", $space->toString());
    }
  }

  private function isExcludedSpace(string $space_name): bool {
    return in_array($space_name, $this->excluded_spaces);
  }

}
