<?php

declare(strict_types=1);

namespace Dependencies;

use Mindee\Dependency\DependencyChecker;
use PHPUnit\Framework\TestCase;

class DependencyCheckerPdfTest extends TestCase
{
    public function testGhostScriptDependency(): void
    {
        $this->expectNotToPerformAssertions();
        DependencyChecker::isGhostscriptAvailable();
    }

    public function testImageMagickDependency(): void
    {
        $this->expectNotToPerformAssertions();
        DependencyChecker::isImageMagickAvailable();
    }

    public function testImageMagickPolicy(): void
    {
        $this->expectNotToPerformAssertions();
        DependencyChecker::isImageMagickPolicyAllowed();
    }
}
