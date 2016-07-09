<?php declare(strict_types=1);

namespace sgolemon\CodeKit\Node;

/**
 * Common utility functons used by various visit_ZEND_AST_* methods
 */
trait Helpers {

  static protected function isValidVarName(string $name): bool {
    return (bool)preg_match('/[_a-zA-Z\x7F-\xFF][_a-zA-Z0-9\x7F-\xFF]*/', $name);
  }

  static protected function isVarBreakChar(string $char): bool {
    return !preg_match('/^[_a-zA-Z0-9\x7F-\xFF]$/', $char);
  }

  protected function visit_DOC_COMMENT(string $comment) {
    if ($comment === '') return;
    $this->ostream->out($comment)->endl()->indent();
  }

  protected function visit_VISIBILITY(int $flags) {
    if ($flags & \AstKit::ZEND_ACC_FINAL)         $this->ostream->out('final ');
    if ($flags & \AstKit::ZEND_ACC_STATIC)        $this->ostream->out('static ');
        if ($flags & \AstKit::ZEND_ACC_PUBLIC)    $this->ostream->out('public ');
    elseif ($flags & \AstKit::ZEND_ACC_PROTECTED) $this->ostream->out('protected ');
    elseif ($flags & \AstKit::ZEND_ACC_PRIVATE)   $this->ostream->out('private ');
    if ($flags & \AstKit::ZEND_ACC_ABSTRACT)      $this->ostream->out('abstract ');
  }

  protected function visit_Name(\AstKit $node) {
    if ($node instanceof \AstKitZval) {
      $val = $node->getValue();
      if (is_string($val)) {
        $this->ostream->out($val);
        return;
      }
    }
    $this->visit($node);
  }

  protected function visit_NSName(\AstKit $node) {
    if ($node instanceof \AstKitZval) {
      $attrs = $node->getAttributes();
      if ($attrs === \AstKit::ZEND_NAME_FQ) {
        $this->ostream->out('\\');
      }
      $this->ostream->out($node->getValue());
    } else {
      $this->visit($node);
    }
  }

  protected function visit_VAR(\AstKit $node) {
    if ($node instanceof \AstKitZval) {
      $val = $node->getValue();
      if (is_string($val) && static::isValidVarName($val)) {
        $this->ostream->out($val);
        return;
      }
    } elseif ($node->getId() === \AstKit::ZEND_AST_VAR) {
      $this->visit_ZEND_AST_VAR($node);
      return;
    }
    $this->ostream->out('{');
    $this->visit($node);
    $this->ostream->out('}');
  }

  static protected function shouldWrap(\AstKit $node): bool {
    switch ($node->getId()) {
      case \AstKit::ZEND_AST_AND:
      case \AstKit::ZEND_AST_BINARY_OP:
      case \AstKit::ZEND_AST_COALESCE:
      case \AstKit::ZEND_AST_CONDITIONAL:
      case \AstKit::ZEND_AST_GREATER:
      case \AstKit::ZEND_AST_GREATER_EQUAL:
      case \AstKit::ZEND_AST_INSTANCEOF:
      case \AstKit::ZEND_AST_OR:
        return true;
      default:
        return false;
    }
  }

  protected function visit_WRAP(\AstKit $parent, int $child) {
    $node = $parent->getChild($child, false);
    // FIXME: This isn't very good, but it's not completely awful either.
    $wrap = static::shouldWrap($node);
    if ($wrap) $this->ostream->out('(');
    $this->visit($node);
    if ($wrap) $this->ostream->out(')');
  }

  protected function visit_PREFIX_OP(string $op, \AstKit $node, int $child = 0) {
    $this->ostream->out($op);
    $this->visit_WRAP($node, $child);
  }

  protected function visit_POSTFIX_OP(string $op, \AstKit $node, int $child = 0) {
    $this->visit_WRAP($node, $child);
    $this->ostream->out($op);
  }

  protected function visit_BINARY_OP(string $op, \AstKit $node, int $left = 0, int $right = 1) {
    $this->visit_WRAP($node, $left);
    $this->ostream->out(' '.$op.' ');
    $this->visit_WRAP($node, $right);
  }

  protected function visit_FUNC_OP(string $func, \AstKit $node, int $child = 0) {
    $this->ostream->out($func . '(');
    $this->visit($node->getChild($child, false));
    $this->ostream->out(')');
  }

  protected function visit_OPTIONAL_OP(string $op, \AstKit $node, int $child = 0) {
    if (null !== $node->getChild($child)) {
      $this->visit_PREFIX_OP($op.' ', $node, $child);
    } else {
      $this->ostream->out($op);
    }
  }

  protected function visit_ENCAPS_STRING(string $str, string $type = '"') {
    $this->ostream->out(addcslashes($str, "\n\r\t\f\v\$\\".$type));
  }

}

