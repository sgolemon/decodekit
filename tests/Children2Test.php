<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Utils.php';

use sgolemon\DecodeKit;

class DecodeKitChildren2Test extends PHPUnit_Framework_TestCase {
  use sgolemon\DecodeKit\Tests\Utils;

  public function testDIM() {
    $this->assertSameAST("\$a[1];\n", AstKit::ZEND_AST_DIM);
    $this->assertSameAST("\$a[];\n", AstKit::ZEND_AST_DIM);
  }

  public function testPROP() {
    $this->assertSameAST("\$a->b;\n", AstKit::ZEND_AST_PROP);
    $this->assertSameAST("\$a->{'1'};\n", AstKit::ZEND_AST_PROP);
  }

  public function testSTATIC_PROP() {
    $this->assertSameAST("A::\$b;\n", AstKit::ZEND_AST_STATIC_PROP);
    $this->assertSameAST("A::\${'1'};\n", AstKit::ZEND_AST_STATIC_PROP);
  }

  public function testCALL() {
    $this->assertSameAST("f();\n", AstKit::ZEND_AST_CALL);
    $this->assertSameAST("f(1, 2, 3);\n", AstKit::ZEND_AST_CALL);
  }

  public function testCLASS_CONST() {
    $this->assertSameAST("A::B;\n", AstKit::ZEND_AST_CLASS_CONST);
    $this->assertSameAST("self::B;\n", AstKit::ZEND_AST_CLASS_CONST);
    $this->assertSameAST("static::B;\n", AstKit::ZEND_AST_CLASS_CONST);
    $this->assertSameAST("C::class;\n", AstKit::ZEND_AST_CLASS_CONST);
  }

  public function testASSIGN() {
    $this->assertSameAST("\$a = 1;\n", AstKit::ZEND_AST_ASSIGN);
    $this->assertSameAST("\$a = \$b = \$c;\n", AstKit::ZEND_AST_ASSIGN);
  }

  public function testASSIGN_REF() {
    $this->assertSameAST("\$a =& \$b;\n", AstKit::ZEND_AST_ASSIGN_REF);
    $this->assertSameAST("\$a = \$b =& \$c;\n", AstKit::ZEND_AST_ASSIGN_REF);
  }

  public function testASSIGN_OP() {
    $ops = array(
      '+=', '-=',  '*=',  '/=',
      '%=', '<<=', '>>=', '.=',
      '|=', '&=',  '^=',  '**=',
    );
    foreach ($ops as $op) {
      $this->assertSameAST("\$a {$op} \$b;\n", AstKit::ZEND_AST_ASSIGN_OP);
    }
  }

  public function testBINARY_OP() {
    $ops = array(
      '+', '-',  '*',  '/',
      '%', '<<', '>>', '.',
      '|', '&',  '^',  '**',
      '===', '!==', '==', '!=',
      '<', '<=', 'xor', '<=>',
    );
    foreach ($ops as $op) {
      $this->assertSameAST("\$a {$op} \$b;\n", AstKit::ZEND_AST_BINARY_OP);
    }
  }

  public function testGREATER() {
    $this->assertSameAST("\$a > \$b;\n", AstKit::ZEND_AST_GREATER);
  }

  public function testGREATER_EQUAL() {
    $this->assertSameAST("\$a >= \$b;\n", AstKit::ZEND_AST_GREATER_EQUAL);
  }

  public function testAND() {
    $this->assertSameAST("\$a && \$b;\n", AstKit::ZEND_AST_AND);
  }

  public function testOR() {
    $this->assertSameAST("\$a || \$b;\n", AstKit::ZEND_AST_OR);
  }

  public function testARRAY_ELEM() {
    $this->assertSameAST("[ 'a' ];\n", AstKit::ZEND_AST_ARRAY_ELEM);
    $this->assertSameAST("[ 1 => 2 ];\n", AstKit::ZEND_AST_ARRAY_ELEM);
  }

  public function testNEW() {
    $this->assertSameAST("new stdClass;\n", AstKit::ZEND_AST_NEW);
    $this->assertSameAST("new stdClass(true);\n", AstKit::ZEND_AST_NEW);
  }

  public function testINSTANCEOF() {
    $this->assertSameAST("\$o instanceof stdClass;\n", AstKit::ZEND_AST_INSTANCEOF);
  }

  public function testYIELD() {
    $this->assertSameAST("yield;\n", AstKit::ZEND_AST_YIELD);
    $this->assertSameAST("yield 1;\n", AstKit::ZEND_AST_YIELD);
    $this->assertSameAST("yield 2 => 3;\n", AstKit::ZEND_AST_YIELD);
  }

  public function testCOALESCE() {
    $this->assertSameAST("\$x ?? false;\n", AstKit::ZEND_AST_COALESCE);
  }

  public function testSTATIC() {
    $this->assertSameAST("static \$a;\n", AstKit::ZEND_AST_STATIC);
  }

  public function testWHILE() {
    $this->assertSameAST("while (true) {\n}\n", AstKit::ZEND_AST_WHILE);
    $this->assertSameAST("while (false) {\n  echo 'hi';\n}\n", AstKit::ZEND_AST_WHILE);
  }

  public function testDO_WHILE() {
    $this->assertSameAST("do {\n} while (true);\n", AstKit::ZEND_AST_DO_WHILE);
    $this->assertSameAST("do {\n  echo 'hi';\n} while (false);\n", AstKit::ZEND_AST_DO_WHILE);
  }

  public function testIF_ELEM() {
    $this->assertSameAST("if (\$a) {\n  echo 'hi';\n}\n", AstKit::ZEND_AST_IF_ELEM);
    $this->assertSameAST("if (\$a) {\n  echo 'hi';\n} elseif (\$b) {\n  echo 'bi';\n}\n", AstKit::ZEND_AST_IF_ELEM);
    $this->assertSameAST("if (\$a) {\n  echo 'hi';\n} elseif (\$b) {\n  echo 'bi';\n} else {\n  echo 'tri';\n}\n", AstKit::ZEND_AST_IF_ELEM);
  }

  public function testSWITCH() {
    $this->assertSameAST("switch (true) {\n}\n", AstKit::ZEND_AST_SWITCH);
  }

  public function testSWITCH_CASE() {
    $this->assertSameAST("switch (true) {\n  case 1:\n    break;\n}\n", AstKit::ZEND_AST_SWITCH_CASE);
    $this->assertSameAST("switch (true) {\n  default:\n}\n", AstKit::ZEND_AST_SWITCH_CASE);
  }

  public function testDECLARE() {
    $this->assertSameAST("declare(strict_types = 1, foo = 'bar');\n", AstKit::ZEND_AST_DECLARE);
    $this->assertSameAST("declare(ticks = foo) {\n}\n", AstKit::ZEND_AST_DECLARE);
  }

  public function testUSE_TRAIT() {
    $this->assertSameAST("use foo\bar;\n", AstKit::ZEND_AST_USE);
    $this->assertSameAST("use foo\ { bar, baz };\n", AstKit::ZEND_AST_USE);
  }

  public function testTRAIT_PRECEDENCE() {
    $this->assertSameAST("class C {\n  use A, B {\n    A::foo insteadof B;\n  }\n}\n", AstKit::ZEND_AST_TRAIT_PRECEDENCE);
  }

  public function testMETHOD_REFERENCE() {
    $this->assertSameAST("class C {\n  use A, B {\n    A::foo as bar;\n  }\n}\n", AstKit::ZEND_AST_METHOD_REFERENCE);
  }

  public function testNAMESPACE() {
    $this->assertSameAST("namespace foo;\n", AstKit::ZEND_AST_NAMESPACE);
    $this->assertSameAST("namespace bar {\n}\n", AstKit::ZEND_AST_NAMESPACE);
  }

  public function testUSE_ELEM() {
    $this->assertSameAST("use foo\bar;\n", AstKit::ZEND_AST_USE_ELEM);
    $this->assertSameAST("use foo as bar;\n", AstKit::ZEND_AST_USE_ELEM);
  }

  public function testTRAIT_ALIAS() {
    $this->assertSameAST("class C {\n  use A, B {\n    A::foo as bar;\n  }\n}\n", AstKit::ZEND_AST_TRAIT_ALIAS);
  }

  public function testGROUP_USE() {
    $this->assertSameAST("use foo\ { bar, baz };\n", AstKit::ZEND_AST_GROUP_USE);
  }
}
