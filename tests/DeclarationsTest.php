<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Utils.php';

use sgolemon\DecodeKit;

class DecodeKitDeclarationsTest extends PHPUnit_Framework_TestCase {
  use sgolemon\DecodeKit\Tests\Utils;

  public function testFUNC_DECL() {
    $this->assertSameAST("function f() {\n}\n", AstKit::ZEND_AST_FUNC_DECL);
    $this->assertSameAST("function f(\$a) {\n  echo 'hi';\n}\n", AstKit::ZEND_AST_FUNC_DECL);
    $this->assertSameAST("function &f() {\n}\n", AstKit::ZEND_AST_FUNC_DECL);
    $this->assertSameAST("function f(): bool {\n  return true;\n}\n", AstKit::ZEND_AST_FUNC_DECL);
    $this->assertSameAST("function f(bool \$b, int \$i, float \$f, array \$a, &\$ref, stdClass \$o, ...\$var) {\n}\n", AstKit::ZEND_AST_FUNC_DECL);

    $this->assertSameAST("/**\n * Awesome function\n */\nfunction f() {\n}\n", AstKit::ZEND_AST_FUNC_DECL);
    $this->assertSameAST("if (true) {\n  /**\n   * Awesome function\n   */\n  function f() {\n  }\n}\n", AstKit::ZEND_AST_FUNC_DECL);
  }

  public function testCLOSURE() {
    $this->assertSameAST("function () {\n  return;\n};\n", AstKit::ZEND_AST_CLOSURE);
    $this->assertSameAST("function (\$a): bool {\n  return true;\n};\n", AstKit::ZEND_AST_CLOSURE);
    $this->assertSameAST("function () use (\$a) {\n  return;\n};\n", AstKit::ZEND_AST_CLOSURE);
    $this->assertSameAST("function () use (\$a): int {\n  return 1;\n};\n", AstKit::ZEND_AST_CLOSURE);
  }

  public function testMETHOD() {
    $this->assertSameAST("class C {\n  protected abstract function w();\n}\n", AstKit::ZEND_AST_METHOD);
  }

  public function testCLASS() {
    $this->assertSameAST("class C {\n}\n", AstKit::ZEND_AST_CLASS);
    $this->assertSameAST("class C extends E implements I, J, K {\n}\n", AstKit::ZEND_AST_CLASS);
    $this->assertSameAST("class C {\n  public \$pub;\n  const K = 1;\n  protected function x(\$y): Z {\n".
                         "    echo 'hi';\n  }\n  protected abstract function w();\n}\n", AstKit::ZEND_AST_CLASS);

    $this->assertSameAST("interface IFoo {\n}\n", AstKit::ZEND_AST_CLASS);
    $this->assertSameAST("trait TFoo {\n}\n", AstKit::ZEND_AST_CLASS);

    $this->assertSameAST("/**\n * Awesome class\n */\nclass C {\n}\n", AstKit::ZEND_AST_CLASS);
    $this->assertSameAST("if (true) {\n  /**\n   * Awesome class\n   */\n  class C {\n  }\n}\n", AstKit::ZEND_AST_CLASS);
  }
}
