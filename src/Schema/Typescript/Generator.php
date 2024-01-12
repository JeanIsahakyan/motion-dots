<?php

namespace MotionDots\Schema\Typescript;

use MotionDots\Process\Processor;
use MotionDots\Schema\Typescript\Nodes\MethodsNode;

class Generator {

  /** @var string[] */
  private array $excluded_spaces = [
    'system',
  ];

  private bool $is_verbose = true;

  public static function create(): self {
    return new self();
  }

  public function generate(Processor $processor, string $files_folder): void {
    $files_folder = rtrim($files_folder, '/');

    $response = $processor->execute('system.getSchema');
    $schema = $response['response'];
    $separator = $processor->getSeparator();

    $realpath = realpath($files_folder);
    $this->echo("Generating Typescript schema in {$realpath}/");

    foreach ($schema as $space_schema) {
      $space_name = (string)$space_schema['name'];
      if ($this->isExcludedSpace($space_name)) {
        continue;
      }
      $space = new MethodsNode($space_schema, $separator);
      $file_name = "{$files_folder}/{$space_name}.d.ts";
      $is_success = (bool)file_put_contents($file_name, $space->toString());
      if (!$is_success) {
        $this->echo("🥵  Error {$space_name} => {$file_name}");
        break;
      }
      $this->echo("   {$space_name} => {$file_name}");
    }

    $this->echo("✨ Done");
  }

  public function setIsVerbose(bool $is_verbose): self {
    $this->is_verbose = $is_verbose;
    return $this;
  }

  public function excludeSpaces(string ...$space_names): self {
    $this->excluded_spaces = array_merge($this->excluded_spaces, $space_names);
    return $this;
  }

  private function isExcludedSpace(string $space_name): bool {
    return in_array($space_name, $this->excluded_spaces);
  }

  private function echo(string $message): void {
    if ($this->is_verbose) {
      echo $message . "\n";
    }
  }

}
