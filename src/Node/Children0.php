<?php declare(strict_types=1);

namespace sgolemon\CodeKit\Node;

/**
 * AST nodes which have zero children
 */
trait Children0 {
  protected function visit_ZEND_AST_MAGIC_CONST(\AstKit $node) {
    $magic = array(
      T_LINE     => '__LINE__',
      T_FILE     => '__FILE__',
      T_DIR      => '__DIR__',
      T_TRAIT_C  => '__TRAIT__',
      T_METHOD_C => '__METHOD__',
      T_FUNC_C   => '__FUNCTION__',
      T_NS_C     => '__NAMESPACE__',
      T_CLASS_C  => '__CLASS__',
    );

    $attr = $node->getAttributes();
    if (array_key_exists($attr, $magic)) {
      $this->ostream->out($magic[$attr]);
    }
  }

  protected function visit_ZEND_AST_TYPE(\AstKit $node) {
    switch ($node->getAttributes()) {
      case \AstKit::IS_ARRAY:    $this->ostream->out('array');    break;
      case \AstKit::IS_CALLABLE: $this->ostream->out('callable'); break;
    }
  }
}

