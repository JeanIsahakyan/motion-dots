<?php

namespace MotionDots\Schema\Typescript;

class FileWriter {

  public static function write(string $filename, string $content): void {
    $resource = fopen($filename, 'w');
    fwrite($resource, $content);
    fclose($resource);
  }

}
