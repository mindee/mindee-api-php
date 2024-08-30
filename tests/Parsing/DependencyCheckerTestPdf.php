<?php

namespace Parsing;

use Mindee\Parsing\DependencyChecker;
use PHPUnit\Framework\TestCase;

class DependencyCheckerPdfTest extends TestCase {
    public function testGhostScriptDependency() {
        $this->assertTrue(DependencyChecker::isGhostscriptAvailable());
    }

    public function testImageMagickDependency() {
        $this->assertTrue(DependencyChecker::isImageMagickAvailable());
    }

    public function testImageMagickPolicy() {
        $this->assertTrue(DependencyChecker::isImageMagickPolicyAllowed());
    }
}
