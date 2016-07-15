<?php declare(strict_types=1);

namespace sgolemon\DecodeKit\Node;

/**
 * AST nodes which have two children
 */
trait Children2 {
  protected function visit_ZEND_AST_DIM(\AstKit $node) {
    $this->visit($node->getChild(0, false));
    $this->ostream->out('[');
    if ($dim = $node->getChild(1, false)) {
      $this->visit($dim);
    }
    $this->ostream->out(']');
  }

  protected function visit_ZEND_AST_PROP(\AstKit $node) {
    $this->visit($node->getChild(0, false));
    $this->ostream->out('->');
    $this->visit_VAR($node->getChild(1, false));
  }

  protected function visit_ZEND_AST_STATIC_PROP(\AstKit $node) {
    $this->visit_NSName($node->getChild(0, false));
    $this->ostream->out('::$');
    $this->visit_VAR($node->getChild(1, false));
  }

  protected function visit_ZEND_AST_CALL(\AstKit $node) {
    $this->visit_NSName($node->getChild(0, false));
    $this->ostream->out('(');
    $this->visit($node->getChild(1, false));
    $this->ostream->out(')');
  }

  protected function visit_ZEND_AST_CLASS_CONST(\AstKit $node) {
    $this->visit_NSName($node->getChild(0, false));
    $this->ostream->out('::');
    $this->visit_Name($node->getChild(1, false));
  }

  protected function visit_ZEND_AST_ASSIGN(\AstKit $node) {
    $this->visit_BINARY_OP('=', $node);
  }

  protected function visit_ZEND_AST_ASSIGN_REF(\AstKit $node) {
    $this->visit_BINARY_OP('=&', $node);
  }

  protected function visit_ZEND_AST_ASSIGN_OP(\AstKit $node) {
    $ops = array(
      \AstKit::ZEND_ASSIGN_ADD    => '+=',
      \AstKit::ZEND_ASSIGN_SUB    => '-=',
      \AstKit::ZEND_ASSIGN_MUL    => '*=',
      \AstKit::ZEND_ASSIGN_DIV    => '/=',
      \AstKit::ZEND_ASSIGN_MOD    => '%=',
      \AstKit::ZEND_ASSIGN_SL     => '<<=',
      \AstKit::ZEND_ASSIGN_SR     => '>>=',
      \AstKit::ZEND_ASSIGN_CONCAT => '.=',
      \AstKit::ZEND_ASSIGN_BW_OR  => '|=',
      \AstKit::ZEND_ASSIGN_BW_AND => '&=',
      \AstKit::ZEND_ASSIGN_BW_XOR => '^=',
      \AstKit::ZEND_ASSIGN_POW    => '**=',
    );
    $attr = $node->getAttributes();
    if (array_key_exists($attr, $ops)) {
      $this->visit_BINARY_OP($ops[$attr], $node);
    }
  }

  protected function visit_ZEND_AST_BINARY_OP(\AstKit $node) {
    $ops = array(
      \AstKit::ZEND_ADD                 => '+',
      \AstKit::ZEND_SUB                 => '-',
      \AstKit::ZEND_MUL                 => '*',
      \AstKit::ZEND_DIV                 => '/',
      \AstKit::ZEND_MOD                 => '%',
      \AstKit::ZEND_SL                  => '<<',
      \AstKit::ZEND_SR                  => '>>',
      \AstKit::ZEND_CONCAT              => '.',
      \AstKit::ZEND_BW_OR               => '|',
      \AstKit::ZEND_BW_AND              => '&',
      \AstKit::ZEND_BW_XOR              => '^',
      \AstKit::ZEND_IS_IDENTICAL        => '===',
      \AstKit::ZEND_IS_NOT_IDENTICAL    => '!==',
      \AstKit::ZEND_IS_EQUAL            => '==',
      \AstKit::ZEND_IS_NOT_EQUAL        => '!=',
      \AstKit::ZEND_IS_SMALLER          => '<',
      \AstKit::ZEND_IS_SMALLER_OR_EQUAL => '<=',
      \AstKit::ZEND_POW                 => '**',
      \AstKit::ZEND_BOOL_XOR            => 'xor',
      \AstKit::ZEND_SPACESHIP           => '<=>',
    );
    $attr = $node->getAttributes();
    if (array_key_exists($attr, $ops)) {
      $this->visit_BINARY_OP($ops[$attr], $node);
    }
  }

  protected function visit_ZEND_AST_GREATER(\AstKit $node) {
    $this->visit_BINARY_OP('>', $node);
  }

  protected function visit_ZEND_AST_GREATER_EQUAL(\AstKit $node) {
    $this->visit_BINARY_OP('>=', $node);
  }

  protected function visit_ZEND_AST_AND(\AstKit $node) {
    $this->visit_BINARY_OP('&&', $node);
  }

  protected function visit_ZEND_AST_OR(\AstKit $node) {
    $this->visit_BINARY_OP('||', $node);
  }

  protected function visit_ZEND_AST_ARRAY_ELEM(\AstKit $node) {
    if ($key = $node->getChild(1, false)) {
      $this->visit($key);
      $this->ostream->out(' => ');
    }
    $this->visit($node->getChild(0, false));
  }

  protected function visit_ZEND_AST_NEW(\AstKit $node) {
    $this->ostream->out('new ');
    $class = $node->getChild(0, false);
    $anonymous = $class->getId() === \AstKit::ZEND_AST_CLASS;
    if ($anonymous) {
      $this->ostream->out('class');
    } else {
      $this->visit_NSName($class);
    }
    if (($ctorArgs = $node->getChild(1)) &&
        ($ctorArgs->numChildren() > 0)) {
      $this->ostream->out('(');
      $this->visit($ctorArgs);
      $this->ostream->out(')');
    }
    if ($anonymous) {
      $this->visit_DECL_CLASS_NO_NAME($class);
    }
  }

  protected function visit_ZEND_AST_INSTANCEOF(\AstKit $node) {
    // Not quite a normal BINARY_OP since child[1] is a name, not a node
    $this->visit($node->getChild(0, false));
    $this->ostream->out(' instanceof ');
    $this->visit_NSName($node->getChild(1, false));
  }

  protected function visit_ZEND_AST_YIELD(\AstKit $node) {
    $this->ostream->out('yield');
    if ($val = $node->getChild(0, false)) {
      $this->ostream->out(' ');
      if ($key = $node->getChild(1, false)) {
        $this->visit($key);
        $this->ostream->out(' => ');
      }
      $this->visit($val);
    }
  }

  protected function visit_ZEND_AST_COALESCE(\AstKit $node) {
    $this->visit_BINARY_OP('??', $node);
  }

  protected function visit_ZEND_AST_STATIC(\AstKit $node) {
    $this->ostream->out('static $');
    $this->visit_VAR($node->getChild(0, false));
  }

  protected function visit_ZEND_AST_WHILE(\AstKit $node) {
    $this->ostream->out('while (');
    $this->visit($node->getChild(0, false));
    $this->ostream->out(') {')->endl();
    $this->ostream->indentInc();
    $this->visit($node->getChild(1, false));
    $this->ostream->indentDec();
    $this->ostream->indent()->out('}');
  }

  protected function visit_ZEND_AST_DO_WHILE(\AstKit $node) {
    $this->ostream->out('do {')->endl();
    $this->ostream->indentInc();
    $this->visit($node->getChild(0, false));
    $this->ostream->indentDec();
    $this->ostream->indent()->out('} while (');
    $this->visit($node->getChild(1, false));
    $this->ostream->out(')');
  }

  protected function visit_ZEND_AST_IF_ELEM(\AstKit $node) {
    if ($cond = $node->getChild(0, false)) {
      $this->ostream->out('if (');
      $this->visit($cond);
      $this->ostream->out(') {')->endl();
    } else {
      $this->ostream->out('else {')->endl();
    }
    $this->ostream->indentInc();
    $this->visit($node->getChild(1, false));
    $this->ostream->indentDec();
    $this->ostream->indent()->out('}');
  }

  protected function visit_ZEND_AST_SWITCH(\AstKit $node) {
    $this->ostream->out('switch (');
    $this->visit($node->getChild(0, false));
    $this->ostream->out(') {')->endl();

    $this->ostream->indentInc();
    $this->visit($node->getChild(1, false));
    $this->ostream->indentDec();
    $this->ostream->indent()->out('}');
  }

  protected function visit_ZEND_AST_SWITCH_CASE(\AstKit $node) {
    if ($case = $node->getChild(0, false)) {
      $this->ostream->out('case ');
      $this->visit($case);
      $this->ostream->out(':')->endl();
    } else {
      $this->ostream->out('default:')->endl();
    }

    $this->ostream->indentInc();
    $this->visit($node->getChild(1, false));
    $this->ostream->indentDec();
  }

  protected function visit_ZEND_AST_DECLARE(\AstKit $node) {
    $this->ostream->out('declare(');
    $const = $node->getChild(0, false);
    assert($const->getId() === \AstKit::ZEND_AST_CONST_DECL);
    $this->visit_LIST_SIMPLE($const);
    if ($block = $node->getChild(1, false)) {
      $this->ostream->out(') {')->endl();
      $this->ostream->indentInc();
      $this->visit($block);
      $this->ostream->indentDec();
      $this->ostream->indent()->out('}');
    } else {
      $this->ostream->out(');');
    }
  }

  protected function visit_ZEND_AST_USE_TRAIT(\AstKit $node) {
    $this->ostream->out('use ');
    $this->visit_Name($node->getChild(0, false));
    if ($group = $node->getChild(1, false)) {
      $this->ostream->out(' {')->endl();
      $this->ostream->indentInc();
      $this->visit($group);
      $this->ostream->indentDec();
      $this->ostream->indent()->out('}');
    } else {
      $this->ostream->out(';');
    }
  }

  protected function visit_ZEND_AST_TRAIT_PRECEDENCE(\AstKit $node) {
    $this->visit_BINARY_OP('insteadof', $node);
  }

  protected function visit_ZEND_AST_METHOD_REFERENCE(\AstKit $node) {
    if ($class = $node->getChild(0, false)) {
      $this->visit_Name($class);
      $this->ostream->out('::');
    }
    $this->visit_Name($node->getChild(1, false));
  }

  protected function visit_ZEND_AST_NAMESPACE(\AstKit $node) {
    $this->ostream->out('namespace');
    if ($name = $node->getChild(0, false)) {
      $this->ostream->out(' ');
      $this->visit_Name($name);
    }
    if ($block = $node->getChild(1, false)) {
      $this->ostream->out(' {')->endl();
      $this->visit($block);
      $this->ostream->indent()->out('}');
    } else {
      $this->ostream->out(';');
    }
  }

  protected function visit_ZEND_AST_USE_ELEM(\AstKit $node) {
    switch ($node->getAttributes()) {
      case T_FUNCTION: $this->ostream->out('function ');
      case T_CONST: $this->ostream->out('const ');
      // Else class, which is unprefixed
    }
    $this->visit_Name($node->getChild(0, false));
    if ($as = $node->getChild(1, false)) {
      $this->ostream->out(' as ');
      $this->visit_Name($as);
    }
  }

  protected function visit_ZEND_AST_TRAIT_ALIAS(\AstKit $node) {
    $this->visit_Name($node->getChild(0, false));
    $attr = $node->getAttributes();
    $target = $node->getChild(1, false);
    if ($attr & \AstKit::ZEND_ACC_PUBLIC) {
      $this->ostream->out(' as public');
    } elseif ($attr & \AstKit::ZEND_ACC_PROTECTED) {
      $this->ostream->out(' as protected');
    } elseif ($attr & \AstKit::ZEND_ACC_PRIVATE) {
      $this->ostream->out(' as private');
    } elseif ($target) {
      $this->ostream->out(' as');
    }
    if ($target) {
      $this->ostream->out(' ');
      $this->visit_Name($target);
    }
  }

  protected function visit_ZEND_AST_GROUP_USE(\AstKit $node) {
    $this->ostream->out('use ');
    $this->visit_Name($node->getChild(0, false));
    $list = $node->getChild(1, false);
    if ($list->numChildren()) {
      $this->ostream->out('\\ { ');

      $this->ostream->indentInc();
      $this->visit($node->getChild(1, false));
      $this->ostream->indentDec();

      $this->ostream->indent()->out(' }');
    } else {
      $this->ostream->out('\\ {}');
    }
  }
}
