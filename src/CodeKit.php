<?php declare(strict_types=1);

namespace sgolemon\CodeKit;

/**
 * Controller for AST dissasembly
 *
 * This class will dissasemble an AstKit node to
 * its original source code.
 *
 * Extend this class and override node visitor methods
 * (e.g. vist_ZEND_AST_FUNC_DECL) to alter how function nodes are decoded
 */
class CodeKit {
  // The several AST node types are split up into traits
  // to avoid having this file grow out of control
  use Node\Helpers;
  use Node\Specials;
  use Node\Declarations;
  use Node\Lists;
  use Node\Children0;
  use Node\Children1;
  use Node\Children2;
  use Node\Children3;
  use Node\Children4;

  /**
   * The View for this decoder
   * @type View\Base
   */
  protected $ostream;

  /**
   * Options array as passed to constructor
   * @type array
   */
  protected $options;

  /**
   * Instantiate a new decoder
   *
   * @param View\Base $ostream - Handler for output
   * @param array $options - Arbitrary dictionary of options data, used by base controller
   */
  public function __construct(View\Base $ostream, array $options = array()) {
    $this->ostream = $ostream;
    $this->options = $options;
  }

  public function visit(\AstKit $node = null) {
    if ($node === null) return;
    $name = \AstKit::kindName($node->getId());
    $method = 'visit_' . $name;
    if (!is_callable([$this, $method])) {
      throw new \ErrorException("Unknown node type {$node->getId()}: {$name}");
    }
    $this->$method($node);
  }

  public function flush() {
    return $this->ostream->flush();
  }
}
