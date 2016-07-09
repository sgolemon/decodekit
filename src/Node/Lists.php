<?php declare(strict_types=1);

namespace sgolemon\CodeKit\Node;

/**
 * Parameter lists, for expressions, if conditions, etc...
 */
trait Lists {
  static protected function isStatement(\AstKit $node): bool {
    switch ($node->getId()) {
      case \AstKit::ZEND_AST_LABEL:
      case \AstKit::ZEND_AST_IF:
      case \AstKit::ZEND_AST_SWITCH:
      case \AstKit::ZEND_AST_WHILE:
      case \AstKit::ZEND_AST_TRY:
      case \AstKit::ZEND_AST_FOR:
      case \AstKit::ZEND_AST_FOREACH:
      case \AstKit::ZEND_AST_FUNC_DECL:
      case \AstKit::ZEND_AST_METHOD:
      case \AstKit::ZEND_AST_CLASS:
      case \AstKit::ZEND_AST_USE_TRAIT:
      case \AstKit::ZEND_AST_NAMESPACE:
      case \AstKit::ZEND_AST_DECLARE:
        return true;
    }
    return false;
  }

  protected function visit_LIST_STMT(\AstKitList $list) {
    $count = $list->numChildren();
    $stmt_list = $list->getId() === \AstKit::ZEND_AST_STMT_LIST;
    for ($i = 0; $i < $count; ++$i) {
      $node = $list->getChild($i, false);
      if ($node === null) continue;
      $id = $node->getId();
      if (($id !== \AstKit::ZEND_AST_STMT_LIST) &&
          ($it !== \AstKit::ZEND_AST_LABEL)) {
        $this->ostream->indent();
      }
      $this->visit($node);
      if ($stmt_list && ($node->getId() === \AstKit::ZEND_AST_STMT_LIST)) continue;
      if (!static::isStatement($node)) $this->ostream->out(';');
      $this->ostream->endl();
    }
  }

  protected function visit_LIST_SIMPLE(\AstKitList $list, string $separator = ', ') {
    $count = $list->numChildren();
    for ($i = 0; $i < $count; ++$i) {
      if ($i > 0) $this->ostream->out($separator);
      $this->visit($list->getChild($i, false));
    }
  }

  protected function visit_LIST_NAME(\AstKitList $list, string $separator = ', ') {
    $count = $list->numChildren();
    for ($i = 0; $i < $count; ++$i) {
      if ($i > 0) $this->ostream->out($separator);
      $this->visit_NSName($list->getChild($i, false));
    }
  }

  // ------------------------------------------------------------------------

  protected function visit_ZEND_AST_ARG_LIST(\AstKitList $list) {
    $this->visit_LIST_SIMPLE($list);
  }

  protected function visit_ZEND_AST_ARRAY(\AstKitList $list) {
    $short = (PHP_VERSION_ID < 70100) || ($list->getAttributes() === \AstKit::ZEND_ARRAY_SYNTAX_SHORT);
    if ($list->numChildren()) {
      $this->ostream->out($short ? '[ ' : 'array(');
      $this->visit_LIST_SIMPLE($list);
      $this->ostream->out($short ? ' ]' : ')');
    } else {
      $this->ostream->out($short ? '[]' : 'array()');
    }
  }

  protected function visit_ZEND_AST_ENCAPS_LIST(\AstKitList $list, string $delimiter = '"') {
    $this->ostream->out($delimiter);
    $count = $list->numChildren();
    for ($i = 0; $i < $count; ++$i) {
      $child = $list->getChild($i, false);
      if ($child instanceof \AstKitZval) {
        $this->visit_ENCAPS_STRING($child->getValue(), $delimiter);
        continue;
      }
      if (($child->getId() === \AstKit::ZEND_AST_VAR) &&
          ($child->getChild(0, false) instanceof \AstKitZval)) {
          $nextChild = (($i + 1) < $count) ? $list->getChild($i + 1, false) : null;
          if (!($nextChild instanceof \AstKitZval) ||
              static::isVarBreakChar(substr($nextChild->getValue(), 0, 1))) {
            // Simple interpolated var
            $this->visit($child);
            continue;
          }
      }
      // Otherwise we need to disambiguate the variable reference
      $this->ostream->out('{');
      $this->visit($child);
      $this->ostream->out('}');
    }
    $this->ostream->out($delimiter);
  }

  // PHP 7.0 specific.  In 7.1 it is turned into a ZEND_AST_ARRAY
  protected function visit_ZEND_AST_LIST(\AstKitList $list) {
    $this->ostream->out('list(');
    $this->visit_LIST_SIMPLE($list);
    $this->ostream->out(')');
  }

  protected function visit_ZEND_AST_EXPR_LIST(\AstKitList $list) {
    $this->visit_LIST_SIMPLE($list);
  }

  protected function visit_ZEND_AST_STMT_LIST(\AstKitList $list) {
    $this->visit_LIST_STMT($list);
  }

  protected function visit_ZEND_AST_IF(\AstKitList $list) {
tail_call:
    $count = $list->numChildren();
    for ($i = 0; $i < $count; ++$i) {
      if ($i > 0) $this->ostream->indent();
      $if = $list->getChild($i);
      if ($cond = $if->getChild(0, false)) {
        $this->ostream->out($i ? '} elseif (' : 'if (');
        $this->visit($cond);
        $this->ostream->out(') {')->endl();
        $this->ostream->indentInc();
        $this->visit($if->getChild(1, false));
        $this->ostream->indentDec();
      } else {
        $this->ostream->out('} else ');
        $else = $if->getChild(1);
        if ($else->getId() === \AstKit::ZEND_AST_IF) {
          $list = $else;
          goto tail_call;
        }
        $this->ostream->out('{')->endl()->indentInc();
        $this->visit($else);
        $this->ostream->indentDec();
      }
    }
    $this->ostream->indent()->out('}');
  }

  protected function visit_ZEND_AST_SWITCH_LIST(\AstKitList $list) {
    $count = $list->numChildren();
    for ($i = 0; $i < $count; ++$i) {
      $this->ostream->indent();
      $this->visit($list->getChild($i, false));
    }
  }

  protected function visit_ZEND_AST_CATCH_LIST(\AstKitList $list) {
    $this->visit_LIST_SIMPLE($list, '');
  }

  protected function visit_ZEND_AST_PARAM_LIST(\AstKitList $list) {
    $this->visit_LIST_SIMPLE($list);
  }

  protected function visit_ZEND_AST_CLOSURE_USES(\AstKitList $list) {
    $this->ostream->out(' use (');
    $count = $list->numChildren();
    for ($i = 0; $i < $count; ++$i) {
      if ($i) $this->ostream->out(', ');
      $child = $list->getChild($i, false);
      $this->ostream->out($child->getAttributes() ? '&$' : '$');
      $this->visit_Name($child);
    }
    $this->ostream->out(')');
  }

  protected function visit_ZEND_AST_PROP_DECL(\AstKitList $list) {
    $this->visit_VISIBILITY($list->getAttributes());
    $this->visit_LIST_SIMPLE($list);
  }

  protected function visit_ZEND_AST_CONST_DECL(\AstKitList $list) {
    $this->ostream->out('const ');
    $this->visit_LIST_SIMPLE($list);
  }

  protected function visit_ZEND_AST_CLASS_CONST_DECL(\AstKitList $list) {
    $this->ostream->out('const ');
    $this->visit_LIST_SIMPLE($list);
  }

  protected function visit_ZEND_AST_NAME_LIST(\AstKitList $list) {
    $this->visit_LIST_NAME($list);
  }

  protected function visit_ZEND_AST_TRAIT_ADAPTATIONS(\AstKitList $list) {
    $this->visit_LIST_STMT($list);
  }

  protected function visit_ZEND_AST_USE(\AstKitList $list) {
    $attrs = $list->getAttributes();
    // $attrs === 0 means we're in a group use
    // That's probably not by design, but it works out
    if ($attrs) $this->ostream->out('use ');
    if ($attrs === T_FUNCTION)  $this->ostream->out('function ');
    elseif ($attrs === T_CONST) $this->ostream->out('const ');
    $this->visit_LIST_SIMPLE($list);
  }
}
