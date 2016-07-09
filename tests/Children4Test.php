<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Utils.php';

use sgolemon\CodeKit;

class CodeKitChildren4Test extends PHPUnit_Framework_TestCase {
  use sgolemon\CodeKit\Tests\Utils;

  public function testFOR() {
    $this->assertSameAST("for (\$i = 0; \$i; ++\$i) {\n}\n", AstKit::ZEND_AST_FOR);
    $this->assertSameAST("for (\$i = 0; \$i; ++\$i) {\n  echo 'hi';\n}\n", AstKit::ZEND_AST_FOR);
  }

  public function testFOREACH() {
    $this->assertSameAST("foreach (\$arr as \$val) {\n}\n", AstKit::ZEND_AST_FOREACH);
    $this->assertSameAST("foreach (\$arr as \$key => \$val) {\n}\n", AstKit::ZEND_AST_FOREACH);
    $this->assertSameAST("foreach (\$arr as \$key => \$val) {\n  echo 'hi';\n}\n", AstKit::ZEND_AST_FOREACH);
  }
}
