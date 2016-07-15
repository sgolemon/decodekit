<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Utils.php';

use sgolemon\DecodeKit;

class DecodeKitListsTest extends PHPUnit_Framework_TestCase {
  use sgolemon\DecodeKit\Tests\Utils;

  public function testARG_LIST() {
    $this->assertSameAST("f(1, 2, 3);\n", AstKit::ZEND_AST_ARG_LIST);
  }

  public function testARRAY() {
    $this->assertSameAST("[];\n", AstKit::ZEND_AST_ARRAY);
    $this->assertSameAST("[ 1, 2 => 3 ];\n", AstKit::ZEND_AST_ARRAY);
    if (PHP_VERSION_ID >= 70100) {
      $this->assertSameAST("array();\n", AstKit::ZEND_AST_ARRAY);
      $this->assertSameAST("array(1, 2 => 3);\n", AstKit::ZEND_AST_ARRAY);
    }
  }

  public function testENCAPS_LIST() {
    $this->assertSameAST("\"foo \$bar\";\n", AstKit::ZEND_AST_ENCAPS_LIST);
    $this->assertSameAST("\"foo {\$bar}baz\";\n", AstKit::ZEND_AST_ENCAPS_LIST);
  }

  public function testLIST() {
    if (PHP_VERSION_ID < 70100) {
      $this->assertSameAST("list(\$a) = \$b;\n", AstKit::ZEND_AST_LIST);
      $this->assertSameAST("list(\$a, \$b) = \$c;\n", AstKit::ZEND_AST_LIST);
      $this->assertSameAST("list(\$a, , \$b) = \$c;\n", AstKit::ZEND_AST_LIST);
    } else {
      $this->assertSameAST("[ \$a ] = \$b;\n", AstKit::ZEND_AST_ARRAY);
      $this->assertSameAST("[ \$a, \$b ] = \$b;\n", AstKit::ZEND_AST_ARRAY);
      $this->assertSameAST("[ \$a, , \$b ] = \$b;\n", AstKit::ZEND_AST_ARRAY);
    }
  }

  public function testEXPR_LIST() {
    $this->assertSameAST("for (\$a = 0, \$b = 1; \$a; ++\$b) {\n}\n", AstKit::ZEND_AST_EXPR_LIST);
  }

  public function testSTMT_LIST() {
    $this->assertSameAST("echo 'hi';\necho 'bye';\n", AstKit::ZEND_AST_STMT_LIST);
  }

  public function testIF() {
    $this->assertSameAST("if (0) {\n}\n", AstKit::ZEND_AST_IF);
    $this->assertSameAST("if (1) {\n} elseif (2) {\n} else {\n}\n", AstKit::ZEND_AST_IF);
  }

  public function testSWITCH_LIST() {
    $this->assertSameAST("switch (true) {\n  case FOO:\n    break;\n}\n", AstKit::ZEND_AST_SWITCH_LIST);
  }

  public function testCATCH_LIST() {
    $this->assertSameAST("try {\n} catch (\Exception \$e) {\n  throw \$e;\n}\n", AstKit::ZEND_AST_CATCH_LIST);
    $this->assertSameAST("try {\n} catch (\Exception \$e) {\n  throw \$e;\n".
                         "} catch (\Throwable \$t) {\n} finally {\n}\n", AstKit::ZEND_AST_CATCH_LIST);
  }

  public function testPARAM_LIST() {
    $this->assertSameAST("function f(\$a) {\n}\n", AstKit::ZEND_AST_PARAM_LIST);
    $this->assertSameAST("function f(\$a, \$b, \$c) {\n}\n", AstKit::ZEND_AST_PARAM_LIST);
  }

  public function testCLOSURE_USES() {
    $this->assertSameAST("function () use (\$x, \$y) {\n};\n", AstKit::ZEND_AST_CLOSURE_USES);
  }

  public function testPROP_DECL() {
    $this->assertSameAST("class C {\n  public \$a;\n}\n", AstKit::ZEND_AST_PROP_DECL);
    $this->assertSameAST("class C {\n  protected \$a;\n}\n", AstKit::ZEND_AST_PROP_DECL);
    $this->assertSameAST("class C {\n  private \$a;\n}\n", AstKit::ZEND_AST_PROP_DECL);
    $this->assertSameAST("class C {\n  public \$a = null;\n}\n", AstKit::ZEND_AST_PROP_DECL);
    $this->assertSameAST("class C {\n  public \$a, \$b, \$c;\n}\n", AstKit::ZEND_AST_PROP_DECL);
  }

  public function testCONST_DECL() {
    $this->assertSameAST("const A = 1;\n", AstKit::ZEND_AST_CONST_DECL);
    $this->assertSameAST("const A = 1, B = 2;\n", AstKit::ZEND_AST_CONST_DECL);
  }

  public function testCLASS_CONST_DECL() {
    $this->assertSameAST("class C {\n  const A = 1;\n}\n", AstKit::ZEND_AST_CLASS_CONST_DECL);
    $this->assertSameAST("class C {\n  const A = 1, B = 2;\n}\n", AstKit::ZEND_AST_CLASS_CONST_DECL);
  }

  public function testNAME_LIST() {
    $this->assertSameAST("interface A extends B, C {\n}\n", AstKit::ZEND_AST_NAME_LIST);
  }

  public function testTRAIT_ADAPTATIONS() {
    $this->assertSameAST("class C {\n  use A, B {\n    A::foo insteadof B;\n  }\n}\n", AstKit::ZEND_AST_TRAIT_ADAPTATIONS);
  }

  public function testUSE() {
    $this->assertSameAST("use Foo;\n", AstKit::ZEND_AST_USE);
    $this->assertSameAST("use Foo, Loo;\n", AstKit::ZEND_AST_USE);
    $this->assertSameAST("use function bar;\n", AstKit::ZEND_AST_USE);
    $this->assertSameAST("use const BAZ;\n", AstKit::ZEND_AST_USE);
  }
}
