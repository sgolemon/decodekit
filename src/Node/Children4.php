<?php declare(strict_types=1);

namespace sgolemon\DecodeKit\Node;

/**
 * AST nodes which have four children
 */
trait Children4 {
  protected function visit_ZEND_AST_FOR(\AstKit $node) {
    $this->ostream->out('for (');
    $this->visit($node->getChild(0, false));
    $this->ostream->out(';');
    if ($cond = $node->getChild(1, false)) {
      $this->ostream->out(' ');
      $this->visit($cond);
    }
    $this->ostream->out(';');
    if ($step = $node->getChild(2, false)) {
      $this->ostream->out(' ');
      $this->visit($step);
    }
    $this->ostream->out(') {')->endl();
    $this->ostream->indentInc();
    $this->visit($node->getChild(3, false));
    $this->ostream->indentDec()->indent();
    $this->ostream->out('}');
  }

  protected function visit_ZEND_AST_FOREACH(\AstKit $node) {
    $this->ostream->out('foreach (');
    $this->visit($node->getChild(0, false));
    $this->ostream->out(' as ');
    if ($key = $node->getChild(2, false)) {
      $this->visit($key);
      $this->ostream->out(' => ');
    }
    $this->visit($node->getChild(1, false));
    $this->ostream->out(') {')->endl();
    $this->ostream->indentInc();
    $this->visit($node->getChild(3, false));
    $this->ostream->indentDec()->indent();
    $this->ostream->out('}');
  }
}
