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
        DependencyChecker::requireBernardLedit();
    }
}
