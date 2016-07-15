<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Utils.php';

use sgolemon\DecodeKit;

class DecodeKitTransform0700Test extends PHPUnit_Framework_TestCase {
  use sgolemon\DecodeKit\Tests\Utils;

  protected function assertTransform0700(string $src, string $dest) {
    if (PHP_VERSION_ID >= 70100) {
      $this->assertTransform($src, $dest, sgolemon\DecodeKit\Transform\PHP0700::class);
    }
  }

  public function testListAssign() {
    $this->assertTransform0700('[ $a, $b ] = $c;', "list(\$a, \$b) = \$c;\n");
    $this->assertTransform0700('[ $a, [ $b, $c ] ] = $c;', "list(\$a, list(\$b, \$c)) = \$c;\n");
  }

  public function testListForeach() {
    $this->assertTransform0700('foreach ($a as [ $b, $c ]) {}', "foreach (\$a as list(\$b, \$c)) {\n}\n");
    $this->assertTransform0700('foreach ($a as [ $b, [ $c, $d ] ]) {}', "foreach (\$a as list(\$b, list(\$c, \$d))) {\n}\n");
  }
}
