<?php declare(strict_types=1);

namespace sgolemon\DecodeKit\View;

class StdOut extends Base {
  /**
   * Output a string of arbitrary text (code)
   */
  public function out(string $text): Base {
    echo $text;
    return $this;
  }
}
