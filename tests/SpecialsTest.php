<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Utils.php';

use sgolemon\CodeKit;

class CodeKitSpecialsTest extends PHPUnit_Framework_TestCase {
  use sgolemon\CodeKit\Tests\Utils;

  public function testZVAL() {
    $this->assertSameAST("null;\n", AstKit::ZEND_AST_ZVAL);
    $this->assertSameAST("true;\n", AstKit::ZEND_AST_ZVAL);
    $this->assertSameAST("false;\n", AstKit::ZEND_AST_ZVAL);
    $this->assertSameAST("42;\n", AstKit::ZEND_AST_ZVAL);
    $this->assertSameAST("3.0;\n", AstKit::ZEND_AST_ZVAL);
    $this->assertSameAST("'foo';\n", AstKit::ZEND_AST_ZVAL);
  }
}
