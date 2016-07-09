<?php declare(strict_types=1);

namespace sgolemon\CodeKit\Node;

/**
 * AST nodes which have three children
 */
trait Children3 {
  protected function visit_ZEND_AST_METHOD_CALL(\AstKit $node) {
    $this->visit($node->getChild(0, false));
    $this->ostream->out('->');
    $this->visit_Name($node->getChild(1, false));
    $this->ostream->out('(');
    $this->visit($node->getChild(2, false));
    $this->ostream->out(')');
  }

  protected function visit_ZEND_AST_STATIC_CALL(\AstKit $node) {
    $this->visit_NSName($node->getChild(0, false));
    $this->ostream->out('::');
    $this->visit_Name($node->getChild(1, false));
    $this->ostream->out('(');
    $this->visit($node->getChild(2, false));
    $this->ostream->out(')');
  }

  protected function visit_ZEND_AST_CONDITIONAL(\AstKit $node) {
    $this->visit($node->getChild(0, false));
    if ($truthy = $node->getChild(1, false)) {
      $this->ostream->out(' ? ');
      $this->visit($node->getChild(1, false));
      $this->ostream->out(' : ');
    } else {
      $this->ostream->out(' ?: ');
    }
    $this->visit($node->getChild(2, false));
  }

  protected function visit_ZEND_AST_TRY(\AstKit $node) {
    $this->ostream->out('try {')->endl();
    $this->ostream->indentInc();
    $this->visit($node->getChild(0, false));
    $this->ostream->indentDec()->indent();
    $this->visit($node->getChild(1, false));
    if ($finally = $node->getChild(2, false)) {
      $this->ostream->out('} finally {')->endl();
      $this->ostream->indentInc();
      $this->visit($finally);
      $this->ostream->indentDec()->indent();
    }
    $this->ostream->out('}');
  }

  protected function visit_ZEND_AST_CATCH(\AstKit $node) {
    $this->ostream->out('} catch (');
    if (PHP_VERSION_ID >= 70100) {
      $this->visit_LIST_NAME($node->getChild(0, false), ' | ');
    } else {
      $this->visit_NSName($node->getChild(0, false));
    }
    $this->ostream->out(' $');
    $this->visit_VAR($node->getChild(1, false));
    $this->ostream->out(') {')->endl();
    $this->ostream->indentInc();
    $this->visit($node->getChild(2, false));
    $this->ostream->indentDec()->indent();
  }

  protected function visit_ZEND_AST_PARAM(\AstKit $node) {
    if ($type = $node->getChild(0, false)) {
      $this->visit_NSName($type);
      $this->ostream->out(' ');
    }
    $attr = $node->getAttributes();
    if ($attr & \AstKit::ZEND_PARAM_REF) {
      $this->ostream->out('&');
    }
    if ($attr & \AstKit::ZEND_PARAM_VARIADIC) {
      $this->ostream->out('...');
    }
    $this->ostream->out('$');
    $this->visit_Name($node->getChild(1, false));
    if ($default = $node->getChild(2, false)) {
      $this->ostream->out(' = ');
      $this->visit($default);
    }
  }

  protected function visit_ZEND_AST_PROP_ELEM(\AstKit $node) {
    $this->ostream->out('$');
    $this->visit_Name($node->getChild(0, false));
    if ($init = $node->getChild(1, false)) {
      $this->ostream->out(' = ');
      $this->visit($init);
    }
  }

  protected function visit_ZEND_AST_CONST_ELEM(\AstKit $node) {
    $this->visit_Name($node->getChild(0, false));
    $this->ostream->out(' = ');
    $this->visit($node->getChild(1, false));
  }
}

