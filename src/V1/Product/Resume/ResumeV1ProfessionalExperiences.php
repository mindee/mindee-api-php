<?php

declare(strict_types=1);

namespace Mindee\V1\Product\Resume;

use ArrayObject;
use Stringable;

/**
 * The list of the candidate's professional experiences.
 * @extends ArrayObject<integer, ResumeV1ProfessionalExperience>
 */
class ResumeV1ProfessionalExperiences extends ArrayObject implements Stringable
{
    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction array.
     * @param integer|null $pageId Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $entries = [];
        foreach ($rawPrediction as $entry) {
            $entries[] = new ResumeV1ProfessionalExperience($entry, $pageId);
        }
        parent::__construct($entries);
    }

    /**
     * Creates a line of rST table-compliant string separators.
     *
     * @param string $char Character to use as a separator.
     */
    public static function professionalExperiencesSeparator(string $char): string
    {
        $outStr = "  ";
        $outStr .= "+" . str_repeat($char, 17);
        $outStr .= "+" . str_repeat($char, 12);
        $outStr .= "+" . str_repeat($char, 38);
        $outStr .= "+" . str_repeat($char, 27);
        $outStr .= "+" . str_repeat($char, 11);
        $outStr .= "+" . str_repeat($char, 10);
        $outStr .= "+" . str_repeat($char, 22);
        $outStr .= "+" . str_repeat($char, 13);
        $outStr .= "+" . str_repeat($char, 12);
        return $outStr . "+";
    }


    /**
     * String representation.
     *
     */
    public function __toString(): string
    {
        $lines = "";
        $iterator = $this->getIterator();
        if (!$iterator->valid()) {
            return "";
        }
        while ($iterator->valid()) {
            $entry = $iterator->current();
            $lines .= "\n  " . $entry->toTableLine() . "\n" . self::professionalExperiencesSeparator('-');
            $iterator->next();
        }
        $outStr = "\n" . self::professionalExperiencesSeparator('-') . "\n ";
        $outStr .= " | Contract Type  ";
        $outStr .= " | Department";
        $outStr .= " | Description                         ";
        $outStr .= " | Employer                 ";
        $outStr .= " | End Month";
        $outStr .= " | End Year";
        $outStr .= " | Role                ";
        $outStr .= " | Start Month";
        $outStr .= " | Start Year";
        $outStr .= " |\n" . self::professionalExperiencesSeparator('=');
        $outStr .= $lines;
        return $outStr;
    }
}
