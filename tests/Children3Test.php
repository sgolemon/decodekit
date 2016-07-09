<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Utils.php';

use sgolemon\CodeKit;

class CodeKitChildren3Test extends PHPUnit_Framework_TestCase {
  use sgolemon\CodeKit\Tests\Utils;

  public function testMETHOD_CALL() {
    $this->assertSameAST("\$o->foo();\n", AstKit::ZEND_AST_METHOD_CALL);
    $this->assertSameAST("\$o->foo(1, 2, 3);\n", AstKit::ZEND_AST_METHOD_CALL);
  }

  public function testSTATIC_CALL() {
    $this->assertSameAST("C::foo();\n", AstKit::ZEND_AST_STATIC_CALL);
    $this->assertSameAST("C::foo(1, 2, 3);\n", AstKit::ZEND_AST_STATIC_CALL);
  }

  public function testCONDITIONAL() {
    $this->assertSameAST("\$a ? \$b : \$c;\n", AstKit::ZEND_AST_CONDITIONAL);
    $this->assertSameAST("\$a ?: \$b;\n", AstKit::ZEND_AST_CONDITIONAL);
  }

  public function testTRY() {
    $this->assertSameAST("try {\n  throw \$e;\n}\n", AstKit::ZEND_AST_TRY);
    $this->assertSameAST("try {\n  throw \$e;\n} finally {\n  exit;\n}\n", AstKit::ZEND_AST_TRY);
  }

  public function testCATCH() {
    $this->assertSameAST("try {\n  throw \$e;\n} catch (\\Exception \$ex) {\n}\n", AstKit::ZEND_AST_CATCH);
  }

  public function testPARAM() {
    $this->assertSameAST("function f(\$a) {\n}\n", AstKit::ZEND_AST_PARAM);
    $this->assertSameAST("function f(\$a, &\$b) {\n}\n", AstKit::ZEND_AST_PARAM);
    $this->assertSameAST("function f(\$a, array \$b) {\n}\n", AstKit::ZEND_AST_PARAM);
    $this->assertSameAST("function f(...\$e) {\n}\n", AstKit::ZEND_AST_PARAM);
  }

  public function testPROP_ELEM() {
    $this->assertSameAST("class c {\n  public \$a;\n}\n", AstKit::ZEND_AST_PROP_ELEM);
    $this->assertSameAST("class c {\n  protected \$a;\n}\n", AstKit::ZEND_AST_PROP_ELEM);
    $this->assertSameAST("class c {\n  private \$a;\n}\n", AstKit::ZEND_AST_PROP_ELEM);
    $this->assertSameAST("class c {\n  public \$a, \$b;\n}\n", AstKit::ZEND_AST_PROP_ELEM);
  }

  public function testCONST_ELEM() {
    $this->assertSameAST("class c {\n  const A = 1;\n}\n", AstKit::ZEND_AST_CONST_ELEM);
    $this->assertSameAST("class c {\n  const B = 'foo';\n}\n", AstKit::ZEND_AST_CONST_ELEM);
    $this->assertSameAST("class c {\n  const A = 1, B = 2;\n}\n", AstKit::ZEND_AST_CONST_ELEM);
  }
}
