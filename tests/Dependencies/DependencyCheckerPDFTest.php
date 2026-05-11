<?php

namespace Dependencies;

use Mindee\Dependency\DependencyChecker;
use PHPUnit\Framework\TestCase;

class DependencyCheckerPDFTest extends TestCase {
    public function testGhostScriptDependency() {
        $this->expectNotToPerformAssertions();
        DependencyChecker::isGhostscriptAvailable();
    }

    public function testImageMagickDependency() {
        $this->expectNotToPerformAssertions();
        DependencyChecker::isImageMagickAvailable();
    }

    public function testImageMagickPolicy() {
        $this->expectNotToPerformAssertions();
        DependencyChecker::isImageMagickPolicyAllowed();
    }
}
