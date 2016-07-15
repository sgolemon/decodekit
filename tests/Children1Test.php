<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Utils.php';

use sgolemon\DecodeKit;

class DecodeKitChildren1Test extends PHPUnit_Framework_TestCase {
  use sgolemon\DecodeKit\Tests\Utils;

  public function testVAR() {
    $this->assertSameAST("\$var;\n", AstKit::ZEND_AST_VAR);
    $this->assertSameAST("\${'-'};\n", AstKit::ZEND_AST_VAR);
    $this->assertSameAST("\$\$var;\n", AstKit::ZEND_AST_VAR);
  }

  public function testCONST() {
    $this->assertSameAST("M_PI;\n", AstKit::ZEND_AST_CONST);
    $this->assertSameAST("foo\BAR;\n", AstKit::ZEND_AST_CONST);
  }

  public function testUNPACK() {
    $this->assertSameAST("f(...\$x);\n", AstKit::ZEND_AST_UNPACK);
  }

  public function testUNARY_PLUS() {
    $this->assertSameAST("+1;\n", AstKit::ZEND_AST_UNARY_PLUS);
  }

  public function testUNARY_MINUS() {
    $this->assertSameAST("-1;\n", AstKit::ZEND_AST_UNARY_MINUS);
  }

  public function testCAST() {
    $casts = array(
      '(unset)',
      '(bool)', '(int)', '(float)',
      '(string)', '(array)', '(object)',
    );
    foreach ($casts as $cast) {
      $this->assertSameAST("{$cast}FOO;\n", AstKit::ZEND_AST_CAST);
    }
  }

  public function testEMPTY() {
    $this->assertSameAST("empty(true);\n", AstKit::ZEND_AST_EMPTY);
  }

  public function testISSET() {
    $this->assertSameAST("isset(true);\n", AstKit::ZEND_AST_ISSET);
  }

  public function testSILENCE() {
    $this->assertSameAST("@foo();\n", AstKit::ZEND_AST_SILENCE);
    $this->assertSameAST("@(\$x + \$y);\n", AstKit::ZEND_AST_SILENCE);
  }

  public function testSHELL_EXEC() {
    $this->assertSameAST("`sleep 1`;\n", AstKit::ZEND_AST_SHELL_EXEC);
    $this->assertSameAST("`echo \"\$x\" > /dev/null`;\n", AstKit::ZEND_AST_SHELL_EXEC);
  }

  public function testCLONE() {
    $this->assertSameAST("clone \$x;\n", AstKit::ZEND_AST_CLONE);
  }

  public function testEXIT() {
    $this->assertSameAST("exit(1);\n", AstKit::ZEND_AST_EXIT);
    $this->assertSameAST("exit;\n", AstKit::ZEND_AST_EXIT);
  }

  public function testPRINT() {
    $this->assertSameAST("print 'Hello';\n", AstKit::ZEND_AST_PRINT);
  }

  public function testINCLUDE_OR_EVAL() {
    $ioe = array(
      'include', 'include_once',
      'require', 'require_once',
      'eval',
    );
    foreach ($ioe as $type) {
      $this->assertSameAST("{$type}('/dev/null');\n", AstKit::ZEND_AST_INCLUDE_OR_EVAL);
    }
  }

  public function testUNARY_OP() {
    $this->assertSameAST("~\$foo;\n", AstKit::ZEND_AST_UNARY_OP);
    $this->assertSameAST("~\$bar;\n", AstKit::ZEND_AST_UNARY_OP);
  }

  public function testPRE_INC() {
    $this->assertSameAST("++\$foo;\n", AstKit::ZEND_AST_PRE_INC);
  }

  public function testPOST_INC() {
    $this->assertSameAST("\$foo++;\n", AstKit::ZEND_AST_POST_INC);
  }

  public function testPRE_DEC() {
    $this->assertSameAST("--\$foo;\n", AstKit::ZEND_AST_PRE_DEC);
  }

  public function testPOST_DEC() {
    $this->assertSameAST("\$foo--;\n", AstKit::ZEND_AST_POST_DEC);
  }

  public function testYIELD_FROM() {
    $this->assertSameAST("yield from \$foo;\n", AstKit::ZEND_AST_YIELD_FROM);
  }

  public function testGLOBAL() {
    $this->assertSameAST("global \$foo;\n", AstKit::ZEND_AST_GLOBAL);
  }

  public function testUNSET() {
    $this->assertSameAST("unset(\$foo);\n", AstKit::ZEND_AST_UNSET);
  }

  public function testRETURN() {
    $this->assertSameAST("return \$foo;\n", AstKit::ZEND_AST_RETURN);
    $this->assertSameAST("return;\n", AstKit::ZEND_AST_RETURN);
  }

  public function testLABEL() {
    $this->assertSameAST("loop:\n", AstKit::ZEND_AST_LABEL);
  }

  public function testREF() {
    $this->assertSameAST("foreach (\$x as &\$y) {\n}\n", AstKit::ZEND_AST_REF);
  }

  public function testHALT_COMPILER() {
    $this->assertSameAST("__HALT_COMPILER();\n", AstKit::ZEND_AST_HALT_COMPILER);
  }

  public function testECHO() {
    $this->assertSameAST("echo 1;\n", AstKit::ZEND_AST_ECHO);
  }

  public function testTHROW() {
    $this->assertSameAST("throw \$x;\n", AstKit::ZEND_AST_THROW);
  }

  public function testGOTO() {
    $this->assertSameAST("goto done;\n", AstKit::ZEND_AST_GOTO);
  }

  public function testBREAK() {
    $this->assertSameAST("break;\n", AstKit::ZEND_AST_BREAK);
    $this->assertSameAST("break 1;\n", AstKit::ZEND_AST_BREAK);
  }

  public function testCONTINUE() {
    $this->assertSameAST("continue;\n", AstKit::ZEND_AST_CONTINUE);
    $this->assertSameAST("continue 1;\n", AstKit::ZEND_AST_CONTINUE);
  }
}
