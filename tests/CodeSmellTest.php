<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Utils.php';

use sgolemon\CodeKit;

class CodeKitCodeSmellTest extends PHPUnit_Framework_TestCase {

  protected static function smell(string $code): bool {
    $d = new class(new sgolemon\CodeKit\View\Nil) extends sgolemon\CodeKit\CodeKit {
      public $smells = false;

      // non-instance means its a zval and thus non-variable
      // This can get false-positives, for example: "${'foo'.'bar'}"
      // So it's not perfect, but it's a simple enough example
      protected function visit_ZEND_AST_VAR(\AstKit $node) {
        $this->smells |= $node->getChild(0) instanceof \AstKit;
      }
    };
    $d->visit(\AstKit::parseString($code));
    return $d->smells;
  }

  public function testSmell() {
    $this->assertTrue(static::smell('$$foo;'));
    $this->assertFalse(static::smell('$foo;'));
  }
}
