<?php declare(strict_types=1);

namespace sgolemon\CodeKit\View;

/**
 * Don't actually dissasemble.
 *
 * Useful for testing the disassembler itself
 */
class Nil extends Base {
  public function out(string $text): Base {
    return $this;
  }
}
