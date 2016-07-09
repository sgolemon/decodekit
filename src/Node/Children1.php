<?php declare(strict_types=1);

namespace sgolemon\CodeKit\Node;

/**
 * AST nodes which have one child
 */
trait Children1 {
  protected function visit_ZEND_AST_VAR(\AstKit $node) {
    $child = $node->getChild(0, false);

    $this->ostream->out('$');
    if ($child instanceof \AstKitZval) {
      $name = $child->getValue();
      if (is_string($name) && static::isValidVarName($name)) {
        $this->ostream->out($name);
        return;
      }
    } elseif ($child->getId() === \AstKit::ZEND_AST_VAR) {
      $this->visit($child);
      return;
    }
    $this->ostream->out('{');
    $this->visit($child);
    $this->ostream->out('}');
  }

  protected function visit_ZEND_AST_CONST(\AstKit $node) {
    $this->visit_NSName($node->getChild(0, false));
  }

  protected function visit_ZEND_AST_UNPACK(\AstKit $node) {
    $this->ostream->out('...');
    $this->visit($node->getChild(0, false));
  }

  protected function visit_ZEND_AST_UNARY_PLUS(\AstKit $node) {
    $this->visit_PREFIX_OP('+', $node);
  }

  protected function visit_ZEND_AST_UNARY_MINUS(\AstKit $node) {
    $this->visit_PREFIX_OP('-', $node);
  }

  protected function visit_ZEND_AST_CAST(\AstKit $node) {
    $casts = array(
      \AstKit::IS_NULL   => '(unset)',
      \AstKit::_IS_BOOL  => '(bool)',
      \AstKit::IS_LONG   => '(int)',
      \AstKit::IS_DOUBLE => '(float)',
      \AstKit::IS_STRING => '(string)',
      \AstKit::IS_ARRAY  => '(array)',
      \AstKit::IS_OBJECT => '(object)',
    );
    $attr = $node->getAttributes();
    if (array_key_exists($attr, $casts)) {
      $this->visit_PREFIX_OP($casts[$attr], $node);
    }
  }

  protected function visit_ZEND_AST_EMPTY(\AstKit $node) {
    $this->visit_FUNC_OP('empty', $node);
  }

  protected function visit_ZEND_AST_ISSET(\AstKit $node) {
    $this->visit_FUNC_OP('isset', $node);
  }

  protected function visit_ZEND_AST_SILENCE(\AstKit $node) {
    $this->visit_PREFIX_OP('@', $node);
  }

  protected function visit_ZEND_AST_SHELL_EXEC(\AstKit $node) {
    $child = $node->getChild(0, false);
    if ($child->getId() === \AstKit::ZEND_AST_ENCAPS_LIST) {
      $this->visit_ZEND_AST_ENCAPS_LIST($child, '`');
    } elseif ($child instanceof \AstKitZval) {
      $this->ostream->out('`');
      $this->visit_ENCAPS_STRING($child->getValue(), '`');
      $this->ostream->out('`');
    }
  }

  protected function visit_ZEND_AST_CLONE(\AstKit $node) {
    $this->visit_PREFIX_OP('clone ', $node);
  }

  protected function visit_ZEND_AST_EXIT(\AstKit $node) {
    if ($node->getChild(0, false) !== null) {
      $this->visit_FUNC_OP('exit', $node);
    } else {
      $this->ostream->out('exit');
    }
  }

  protected function visit_ZEND_AST_PRINT(\AstKit $node) {
    $this->visit_PREFIX_OP('print ', $node);
  }

  protected function visit_ZEND_AST_INCLUDE_OR_EVAL(\AstKit $node) {
    $type = array(
      \AstKit::ZEND_INCLUDE      => 'include',
      \AstKit::ZEND_INCLUDE_ONCE => 'include_once',
      \AstKit::ZEND_REQUIRE      => 'require',
      \AstKit::ZEND_REQUIRE_ONCE => 'require_once',
      \AstKit::ZEND_EVAL         => 'eval',
    );
    $attr = $node->getAttributes();
    if (array_key_exists($attr, $type)) {
      $this->visit_FUNC_OP($type[$attr], $node);
    }
  }

  protected function visit_ZEND_AST_UNARY_OP(\AstKit $node) {
    switch ($node->getAttributes()) {
      case \AstKit::ZEND_BW_NOT:   $this->visit_PREFIX_OP('~', $node); break;
      case \AstKit::ZEND_BOOL_NOT: $this->visit_PREFIX_OP('!', $node); break;
    }
  }

  protected function visit_ZEND_AST_PRE_INC(\AstKit $node) {
    $this->visit_PREFIX_OP('++', $node);
  }

  protected function visit_ZEND_AST_POST_INC(\AstKit $node) {
    $this->visit_POSTFIX_OP('++', $node);
  }

  protected function visit_ZEND_AST_PRE_DEC(\AstKit $node) {
    $this->visit_PREFIX_OP('--', $node);
  }

  protected function visit_ZEND_AST_POST_DEC(\AstKit $node) {
    $this->visit_POSTFIX_OP('--', $node);
  }

  protected function visit_ZEND_AST_YIELD_FROM(\AstKit $node) {
    $this->visit_PREFIX_OP('yield from ', $node);
  }

  protected function visit_ZEND_AST_GLOBAL(\AstKit $node) {
    $this->visit_OPTIONAL_OP('global', $node);
  }

  protected function visit_ZEND_AST_UNSET(\AstKit $node) {
    $this->visit_FUNC_OP('unset', $node);
  }

  protected function visit_ZEND_AST_RETURN(\AstKit $node) {
    $this->visit_OPTIONAL_OP('return', $node);
  }

  protected function visit_ZEND_AST_LABEL(\AstKit $node) {
    $this->visit_Name($node->getChild(0, false));
    $this->ostream->out(':');
  }

  protected function visit_ZEND_AST_REF(\AstKit $node) {
    $this->ostream->out('&');
    $this->visit($node->getChild(0, false));
  }

  protected function visit_ZEND_AST_HALT_COMPILER(\AstKit $node) {
    $this->ostream->out('__HALT_COMPILER()');
  }

  protected function visit_ZEND_AST_ECHO(\AstKit $node) {
    $this->ostream->out('echo ');
    $this->visit($node->getChild(0, false));
  }

  protected function visit_ZEND_AST_THROW(\AstKit $node) {
    $this->ostream->out('throw ');
    $this->visit($node->getChild(0, false));
  }

  protected function visit_ZEND_AST_GOTO(\AstKit $node) {
    $this->ostream->out('goto ');
    $this->visit_Name($node->getChild(0, false));
  }

  protected function visit_ZEND_AST_BREAK(\AstKit $node) {
    $this->visit_OPTIONAL_OP('break', $node);
  }

  protected function visit_ZEND_AST_CONTINUE(\AstKit $node) {
    $this->visit_OPTIONAL_OP('continue', $node);
  }
}
