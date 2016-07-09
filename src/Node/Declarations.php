<?php declare(strict_types=1);

namespace sgolemon\CodeKit\Node;

/**
 * Function/Method/Class/etc... Declaration implementations
 */
trait Declarations {

  protected function visit_DECL_FUNCTION(\AstKitDecl $node, bool $closure = false) {
    $this->visit_DOC_COMMENT($node->getDocComment());
    $this->ostream->out('function ');

    $flags = $node->getFlags();
    if ($flags & \AstKit::ZEND_ACC_RETURN_REFERENCE)     $this->ostream->out('&');
    if (!$closure) {
      $this->ostream->out($node->getName());
    }
    $this->ostream->out('(');
    $this->visit($node->getParams());
    $this->ostream->out(')');
    $this->visit($node->getUse());
    if ($returns = $node->getChild(3, false)) {
      $this->ostream->out(': ');
      $this->visit_NSname($returns);
    }
    if ($stmts = $node->getStatements()) {
      $this->ostream->out(' {')->endl();
      $this->ostream->indentInc();
      $this->visit($stmts);
      $this->ostream->indentDec();
      $this->ostream->indent()->out('}');
    } else {
      $this->ostream->out(';');
    }
  }

  protected function visit_DECL_CLASS_NO_NAME(\AstKitDecl $node) {
    $extends = $node->getChild(0, false);
    if ($extends !== null) {
      $this->ostream->out(' extends ');
      $this->visit_NSName($extends);
    }
    $implements = $node->getChild(1, false);
    if ($implements !== null) {
      if ($node->getFlags() & \AstKit::ZEND_ACC_INTERFACE) {
        // Interfaces call this extending, not implementing... whatever
        $this->ostream->out(' extends ');
      } else {
        $this->ostream->out(' implements ');
      }
      $this->visit($implements);
    }
    $this->ostream->out(' {')->endl();

    $this->ostream->indentInc();
    $this->visit($node->getChild(2));
    $this->ostream->indentDec();

    $this->ostream->indent();
    $this->ostream->out('}');
  }

  protected function visit_ZEND_AST_FUNC_DECL(\AstKitDecl $node) {
    $this->visit_DECL_FUNCTION($node);
  }

  protected function visit_ZEND_AST_CLOSURE(\AstKitDecl $node) {
    $this->visit_DECL_FUNCTION($node, true);
  }

  protected function visit_ZEND_AST_METHOD(\AstKitDecl $node) {
    $this->visit_VISIBILITY($node->getFlags());
    $this->visit_DECL_FUNCTION($node);
  }

  protected function visit_ZEND_AST_CLASS(\AstKitDecl $node) {
    $this->visit_DOC_COMMENT($node->getDocComment());
    $flags = $node->getFlags();
    if ($flags & \AstKit::ZEND_ACC_INTERFACE) {
      $this->ostream->out('interface ');
    } elseif ($flags & \AstKit::ZEND_ACC_TRAIT) {
      $this->ostream->out('trait ');
    } else {
      if ($flags & \AstKit::ZEND_ACC_EXPLICIT_ABSTRACT_CLASS) {
        $this->ostream->out('abstract ');
      }
      if ($flags & \AstKit::ZEND_ACC_FINAL) {
        $this->ostream->out('final ');
      }
      $this->ostream->out('class ');
    }
    $this->ostream->out($node->getName());
    $this->visit_DECL_CLASS_NO_NAME($node);
  }

}
