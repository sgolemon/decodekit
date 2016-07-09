<?php

namespace sgolemon\CodeKit\Tests;
use sgolemon\CodeKit;

trait Utils {

  static protected function containsNode(\AstKit $node, int $kind) {
    $count = $node->numChildren();
    for ($i = 0; $i < $count; ++$i) {
      $child = $node->getChild($i, false);
      if ($child === null) continue;
      if ($child->getId() === $kind) return true;
      if (!$child->numChildren()) continue;
      if (static::containsNode($child, $kind)) return true;
    }
    return false;
  }

  protected function assertTransform(string $src, string $dest, string $transform, int $kind = -1) {
    $d = new $transform(new CodeKit\View\Buffer);
    $ast = \AstKit::parseString($src);
    $this->assertNotNull($ast);
    $d->visit($ast);
    $this->assertEquals($dest, $d->flush());
    if ($kind >= 0) {
      $this->assertTrue(self::containsNode($ast, $kind));
    }
  }

  protected function assertSameAST(string $str, int $kind) {
    $this->assertTransform($str, $str, CodeKit\CodeKit::class, $kind);
  }
}
