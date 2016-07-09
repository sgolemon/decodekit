<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Utils.php';

use sgolemon\CodeKit;

class CodeKitChildren0Test extends PHPUnit_Framework_TestCase {
  use sgolemon\CodeKit\Tests\Utils;

  public function testMAGIC_CONST() {
    $magic = array(
      '__LINE__', '__FILE__', '__DIR__',
      '__TRAIT__', '__METHOD__', '__FUNCTION__',
      '__NAMESPACE__', '__CLASS__',
    );
    foreach ($magic as $cns) {
      $this->assertSameAST("$cns;\n", AstKit::ZEND_AST_MAGIC_CONST);
    }
  }

  public function testTYPE() {
    $this->assertSameAST("function f(array \$x) {\n}\n", AstKit::ZEND_AST_TYPE);
    $this->assertSameAST("function f(callable \$x) {\n}\n", AstKit::ZEND_AST_TYPE);
  }
}
