<?php

namespace Mindee\Product\Resume;

/**
 * The list of the candidate's professional experiences.
 */
class ResumeV1ProfessionalExperiences extends \ArrayObject
{
    /**
     * @param array        $rawPrediction Raw prediction array.
     * @param integer|null $pageId        Page number for multi pages document.
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
     * @return string
     */
    public static function professionalExperiencesSeparator(string $char): string
    {
        $outStr = "  ";
        $outStr .= "+" . str_repeat($char, 17);
        $outStr .= "+" . str_repeat($char, 12);
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
     * @return string
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
