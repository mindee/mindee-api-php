<?php

declare(strict_types=1);

namespace Dependencies;

use Mindee\Dependency\DependencyChecker;
use PHPUnit\Framework\TestCase;

class DependencyCheckerPdfTest extends TestCase
{
    public function testBernardLeditDependency(): void
    {
        $this->expectNotToPerformAssertions();
        DependencyChecker::requireBernardLedit();
    }

    public function testBernardLeditVersionMatchesComposerConflict(): void
    {
        $composer = json_decode(file_get_contents(__DIR__ . '/../../composer.json'), true);
        self::assertSame(
            '<' . DependencyChecker::BERNARD_LEDIT_VERSION,
            $composer['conflict']['ext-bernard_ledit']
        );
    }
}
