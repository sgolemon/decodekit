<?php declare(strict_types=1);

namespace sgolemon\CodeKit\Transform;

/**
 * Transform code to be PHP 7.0 compatable
 */
class PHP0700 extends \sgolemon\CodeKit\CodeKit {
  protected $visit_as_list = array();

  /**
   * Deal with list() nesting
   */
  protected function visit_ZEND_AST_LIST(\AstKitList $node) {
    $count = $node->numChildren();
    $this->ostream->out('list(');
    for ($i = 0; $i < $count; ++$i) {
      if ($i > 0) $this->ostream->out(', ');
      $elem = $node->getChild($i, false);
      $child = $elem->getChild(0, false);
      if ($child->getId() === \AstKit::ZEND_AST_ARRAY) {
        $this->visit_as_list[] = $child;
      }
      $this->visit($child);
    }
    $this->ostream->out(')');
  }

  /**
   * Turn an [] array into list() when it's been marked
   */
  protected function visit_ZEND_AST_ARRAY(\AstKitList $node) {
    $key = array_search($node, $this->visit_as_list, true);
    if ($key !== false) {
      unset($this->visit_as_list[$key]);
      return $this->visit_ZEND_AST_LIST($node);
    } else {
      return parent::visit_ZEND_AST_ARRAY($node);
    }
  }

  /**
   * Mark [] array if it's on the lhs of an assign
   */
  protected function visit_ZEND_AST_ASSIGN(\AstKit $node) {
    $list = $node->getChild(0, false);
    if ($list->getId() === \AstKit::ZEND_AST_ARRAY) {
      $this->visit_as_list[] = $list;
    }
    return parent::visit_ZEND_AST_ASSIGN($node);
  }

  /**
   * Mark [] array if it's the value of a foreach() expression
   */
  protected function visit_ZEND_AST_FOREACH(\AstKit $node) {
    $list = $node->getChild(1, false);
    if ($list->getId() === \AstKit::ZEND_AST_ARRAY) {
      $this->visit_as_list[] = $list;
    }
    return parent::visit_ZEND_AST_FOREACH($node);
  }
}

