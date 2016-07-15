<?php declare(strict_types=1);

namespace sgolemon\DecodeKit\Node;

/**
 * Unique AST types.
 * Currently just ZEND_AST_ZVAL
 * since ZEND_AST_ZNDOE is a compile-step psuedo-type
 */
trait Specials {

  protected function visit_ZEND_AST_ZVAL(\AstKitZval $value) {
    $this->ostream->literal($value->getValue());
  }

}

