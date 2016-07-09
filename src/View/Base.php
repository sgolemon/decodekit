<?php declare(strict_types=1);

namespace sgolemon\CodeKit\View;

abstract class Base {
  /**
   * Let the tabs versus spaces war begin
   */
  protected $indentStr = '  ';

  /**
   * Current indentation multiplied
   */
  protected $indent = 0;

  public function __construct(array $options = array()) {
    if (array_key_exists('indent', $options)) {
      $this->indentStr = (string)$options['indent'];
    }
  }

  /**
   * Output a PHP value as a literal, formatting as needed
   */
  public function literal($value): Base {
    return $this->out(var_export($value, true));
  }

  /**
   * Output a string of arbitrary text (code)
   */
  abstract public function out(string $text): Base;

  /**
   * End the current line of output
   */
  public function endl(): Base {
    return $this->out("\n");
  }

  /**
   * Commit any writes and reset any internal buffers
   */
  public function flush() {
    return null;
  }

  /**
   * Indent the output
   */
  public function indent(): Base {
    return $this->out(str_repeat($this->indentStr, $this->indent));
  }

  /**
   * Decrease the current indent level
   *
   * @param int $delta - Number of indent multiples to decrement
   */
  public function indentDec(int $delta = 1): Base {
    $this->indent = max(0, $this->indent - $delta);
    return $this;
  }

  /**
   * Increate the current indent level
   *
   * @param int $delta - Number of indent multiples to increment
   */
  public function indentInc(int $delta = 1): Base {
    $this->indent = max(0, $this->indent + $delta);
    return $this;
  }

}
