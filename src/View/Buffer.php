<?php declare(strict_types=1);

namespace sgolemon\DecodeKit\View;

class Buffer extends Base {
  protected $buffer = '';
  /**
   * Output a string of arbitrary text (code)
   */
  public function out(string $text): Base {
    $this->buffer .= $text;
    return $this;
  }

  public function flush() {
    $ret = $this->buffer;
    $this->buffer = '';
    return $ret;
  }
}
