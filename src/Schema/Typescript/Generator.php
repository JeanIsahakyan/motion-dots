<?php

namespace MotionDots\Schema\Typescript;

use MotionDots\Process\Processor;
use MotionDots\Schema\Typescript\Nodes\MethodNode;
use MotionDots\Schema\Typescript\Repositories\EnumsRepository;
use MotionDots\Schema\Typescript\Repositories\MethodsRepository;
use MotionDots\Schema\Typescript\Repositories\ObjectsRepository;

/**
 * Class Generator
 *
 * @package MotionDots\Schema\Typescript
 *
 * @author  Ermak Aleksandr a@yermak.info
 */
class Generator {

  /** @var string[] */
  private array $excluded_spaces = [
    'system',
  ];

  private bool $is_verbose = true;

  private string $files_path = './api-schema';

  public static function create(): self {
    return new self();
  }

  public function excludeSpaces(string ...$space_names): self {
    $this->excluded_spaces = array_merge($this->excluded_spaces, $space_names);
    return $this;
  }

  private function isExcludedSpace(string $space_name): bool {
    return in_array($space_name, $this->excluded_spaces);
  }

  public function setIsVerbose(bool $is_verbose): self {
    $this->is_verbose = $is_verbose;
    return $this;
  }

  public function setFilesPath(string $files_path): self {
    $files_path = rtrim($files_path, '/');
    $this->files_path = $files_path;
    return $this;
  }

  private function echo(string $message): void {
    if ($this->is_verbose) {
      echo $message . "\n";
    }
  }

  private function filePutContents(string $directory, string $filename, string $contents): void {
    $files_folder = $this->files_path . '/' . $directory;
    if (!file_exists($files_folder)) {
      mkdir($files_folder, 0777, true);
    }

    $file_name = "{$files_folder}/{$filename}.d.ts";

    $header =
      "// This file is a part of api-schema.\n" .
      "// All code below is auto-generated.\n\n";

    $is_success = (bool)file_put_contents($file_name, $header . $contents);
    if (!$is_success) {
      $this->echo("🥵  Error {$filename} => {$file_name}");
    }

    $this->echo("   {$filename} => {$file_name}");
  }

  public function generate(Processor $processor): void {
    if (!file_exists($this->files_path)) {
      mkdir($this->files_path, 0777, true);
    }
    $realpath = realpath($this->files_path);
    $this->echo("Generating Typescript schema in {$realpath}");

    $response = $processor->execute('system.getSchema');
    $schema = (array)$response['response'];
    if (!$schema) {
      $this->echo("🥵  An error has occurred while executing 'system.getSchema'");
    }

    foreach ($schema as $methods_schema) {
      $name = (string)$methods_schema['name'];
      if ($this->isExcludedSpace($name)) {
        continue;
      }
      $methods = [];
      foreach ((array)$methods_schema['methods'] as $method_schema) {
        $method = new MethodNode($method_schema, $processor->getSeparator());
        $methods[] = $method;
      }
      MethodsRepository::add($name, $methods);
    }

    // responses

    $this->echo('Responses:');
    $objects = ObjectsRepository::getAll();
    foreach ($objects as $object) {
      $enum_imports = EnumsRepository::getImports('../enums', $object->getInnerEnums());
      $objects_imports = ObjectsRepository::getImports('./', $object->getInnerObjects());

      $imports_separator = $enum_imports || $objects_imports ? "\n" : '';

      $file_content =
        $enum_imports .
        $objects_imports .
        $imports_separator .
        $object->toString() . "\n";

      $this->filePutContents('responses', $object->getTypeName(), $file_content);
    }

    $object_exports = ObjectsRepository::getExports($objects);
    $this->filePutContents('responses', 'index', $object_exports);

    // enums

    $this->echo('Enums:');
    $enums = EnumsRepository::getAll();
    foreach ($enums as $enum) {
      $this->filePutContents('enums', $enum->getTypeName(), $enum->toString());
    }

    $enums_exports = EnumsRepository::getExports($enums);
    $this->filePutContents('enums', 'index', $enums_exports);

    // methods

    $this->echo('Methods:');
    foreach (MethodsRepository::getSpacesMethods() as $name => $methods) {
      $method_enums = array_merge(...array_map(fn($method) => $method->getEnums(), $methods));
      $enum_imports = EnumsRepository::getImports('../enums', $method_enums);

      $method_objects = array_merge(...array_map(fn($method) => $method->getObjects(), $methods));
      $objects_imports = ObjectsRepository::getImports('../responses', $method_objects);

      $imports_separator = $enum_imports || $objects_imports ? "\n" : '';

      $methods = array_map(fn($method) => $method->toString(), $methods);
      $methods = implode("\n\n", $methods);

      $file_content =
        $enum_imports .
        $objects_imports .
        $imports_separator .
        $methods . "\n";

      $this->filePutContents('methods', $name, $file_content);
    }

    $methods_exports = MethodsRepository::getExports();
    $this->filePutContents('methods', 'index', $methods_exports);

    $this->echo('✨ Done');
  }

}
