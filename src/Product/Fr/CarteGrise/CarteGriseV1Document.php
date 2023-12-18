<?php

namespace Mindee\Product\Fr\CarteGrise;

use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\DateField;
use Mindee\Parsing\Standard\StringField;

/**
 * Document data for Carte Grise, API version 1.
 */
class CarteGriseV1Document extends Prediction
{
    /**
    * @var StringField The vehicle's license plate number.
    */
    public StringField $a;
    /**
    * @var DateField The vehicle's first release date.
    */
    public DateField $b;
    /**
    * @var StringField The vehicle owner's full name including maiden name.
    */
    public StringField $c1;
    /**
    * @var StringField The vehicle owner's address.
    */
    public StringField $c3;
    /**
    * @var StringField Number of owners of the license certificate.
    */
    public StringField $c41;
    /**
    * @var StringField Mentions about the ownership of the vehicle.
    */
    public StringField $c4A;
    /**
    * @var StringField The vehicle's brand.
    */
    public StringField $d1;
    /**
    * @var StringField The vehicle's commercial name.
    */
    public StringField $d3;
    /**
    * @var StringField The Vehicle Identification Number (VIN).
    */
    public StringField $e;
    /**
    * @var StringField The vehicle's maximum admissible weight.
    */
    public StringField $f1;
    /**
    * @var StringField The vehicle's maximum admissible weight within the license's state.
    */
    public StringField $f2;
    /**
    * @var StringField The vehicle's maximum authorized weight with coupling.
    */
    public StringField $f3;
    /**
    * @var StringField The document's formula number.
    */
    public StringField $formulaNumber;
    /**
    * @var StringField The vehicle's weight with coupling if tractor different than category M1.
    */
    public StringField $g;
    /**
    * @var StringField The vehicle's national empty weight.
    */
    public StringField $g1;
    /**
    * @var DateField The car registration date of the given certificate.
    */
    public DateField $i;
    /**
    * @var StringField The vehicle's category.
    */
    public StringField $j;
    /**
    * @var StringField The vehicle's national type.
    */
    public StringField $j1;
    /**
    * @var StringField The vehicle's body type (CE).
    */
    public StringField $j2;
    /**
    * @var StringField The vehicle's body type (National designation).
    */
    public StringField $j3;
    /**
    * @var StringField Machine Readable Zone, first line.
    */
    public StringField $mrz1;
    /**
    * @var StringField Machine Readable Zone, second line.
    */
    public StringField $mrz2;
    /**
    * @var StringField The vehicle's owner first name.
    */
    public StringField $ownerFirstName;
    /**
    * @var StringField The vehicle's owner surname.
    */
    public StringField $ownerSurname;
    /**
    * @var StringField The vehicle engine's displacement (cm3).
    */
    public StringField $p1;
    /**
    * @var StringField The vehicle's maximum net power (kW).
    */
    public StringField $p2;
    /**
    * @var StringField The vehicle's fuel type or energy source.
    */
    public StringField $p3;
    /**
    * @var StringField The vehicle's administrative power (fiscal horsepower).
    */
    public StringField $p6;
    /**
    * @var StringField The vehicle's power to weight ratio.
    */
    public StringField $q;
    /**
    * @var StringField The vehicle's number of seats.
    */
    public StringField $s1;
    /**
    * @var StringField The vehicle's number of standing rooms (person).
    */
    public StringField $s2;
    /**
    * @var StringField The vehicle's sound level (dB).
    */
    public StringField $u1;
    /**
    * @var StringField The vehicle engine's rotation speed (RPM).
    */
    public StringField $u2;
    /**
    * @var StringField The vehicle's CO2 emission (g/km).
    */
    public StringField $v7;
    /**
    * @var StringField Next technical control date.
    */
    public StringField $x1;
    /**
    * @var StringField Amount of the regional proportional tax of the registration (in euros).
    */
    public StringField $y1;
    /**
    * @var StringField Amount of the additional parafiscal tax of the registration (in euros).
    */
    public StringField $y2;
    /**
    * @var StringField Amount of the additional CO2 tax of the registration (in euros).
    */
    public StringField $y3;
    /**
    * @var StringField Amount of the fee for managing the registration (in euros).
    */
    public StringField $y4;
    /**
    * @var StringField Amount of the fee for delivery of the registration certificate in euros.
    */
    public StringField $y5;
    /**
    * @var StringField Total amount of registration fee to be paid in euros.
    */
    public StringField $y6;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $this->a = new StringField(
            $rawPrediction["a"],
            $pageId
        );
        $this->b = new DateField(
            $rawPrediction["b"],
            $pageId
        );
        $this->c1 = new StringField(
            $rawPrediction["c1"],
            $pageId
        );
        $this->c3 = new StringField(
            $rawPrediction["c3"],
            $pageId
        );
        $this->c41 = new StringField(
            $rawPrediction["c41"],
            $pageId
        );
        $this->c4A = new StringField(
            $rawPrediction["c4a"],
            $pageId
        );
        $this->d1 = new StringField(
            $rawPrediction["d1"],
            $pageId
        );
        $this->d3 = new StringField(
            $rawPrediction["d3"],
            $pageId
        );
        $this->e = new StringField(
            $rawPrediction["e"],
            $pageId
        );
        $this->f1 = new StringField(
            $rawPrediction["f1"],
            $pageId
        );
        $this->f2 = new StringField(
            $rawPrediction["f2"],
            $pageId
        );
        $this->f3 = new StringField(
            $rawPrediction["f3"],
            $pageId
        );
        $this->formulaNumber = new StringField(
            $rawPrediction["formula_number"],
            $pageId
        );
        $this->g = new StringField(
            $rawPrediction["g"],
            $pageId
        );
        $this->g1 = new StringField(
            $rawPrediction["g1"],
            $pageId
        );
        $this->i = new DateField(
            $rawPrediction["i"],
            $pageId
        );
        $this->j = new StringField(
            $rawPrediction["j"],
            $pageId
        );
        $this->j1 = new StringField(
            $rawPrediction["j1"],
            $pageId
        );
        $this->j2 = new StringField(
            $rawPrediction["j2"],
            $pageId
        );
        $this->j3 = new StringField(
            $rawPrediction["j3"],
            $pageId
        );
        $this->mrz1 = new StringField(
            $rawPrediction["mrz1"],
            $pageId
        );
        $this->mrz2 = new StringField(
            $rawPrediction["mrz2"],
            $pageId
        );
        $this->ownerFirstName = new StringField(
            $rawPrediction["owner_first_name"],
            $pageId
        );
        $this->ownerSurname = new StringField(
            $rawPrediction["owner_surname"],
            $pageId
        );
        $this->p1 = new StringField(
            $rawPrediction["p1"],
            $pageId
        );
        $this->p2 = new StringField(
            $rawPrediction["p2"],
            $pageId
        );
        $this->p3 = new StringField(
            $rawPrediction["p3"],
            $pageId
        );
        $this->p6 = new StringField(
            $rawPrediction["p6"],
            $pageId
        );
        $this->q = new StringField(
            $rawPrediction["q"],
            $pageId
        );
        $this->s1 = new StringField(
            $rawPrediction["s1"],
            $pageId
        );
        $this->s2 = new StringField(
            $rawPrediction["s2"],
            $pageId
        );
        $this->u1 = new StringField(
            $rawPrediction["u1"],
            $pageId
        );
        $this->u2 = new StringField(
            $rawPrediction["u2"],
            $pageId
        );
        $this->v7 = new StringField(
            $rawPrediction["v7"],
            $pageId
        );
        $this->x1 = new StringField(
            $rawPrediction["x1"],
            $pageId
        );
        $this->y1 = new StringField(
            $rawPrediction["y1"],
            $pageId
        );
        $this->y2 = new StringField(
            $rawPrediction["y2"],
            $pageId
        );
        $this->y3 = new StringField(
            $rawPrediction["y3"],
            $pageId
        );
        $this->y4 = new StringField(
            $rawPrediction["y4"],
            $pageId
        );
        $this->y5 = new StringField(
            $rawPrediction["y5"],
            $pageId
        );
        $this->y6 = new StringField(
            $rawPrediction["y6"],
            $pageId
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {

        $outStr = ":a: $this->a
:b: $this->b
:c1: $this->c1
:c3: $this->c3
:c41: $this->c41
:c4a: $this->c4A
:d1: $this->d1
:d3: $this->d3
:e: $this->e
:f1: $this->f1
:f2: $this->f2
:f3: $this->f3
:g: $this->g
:g1: $this->g1
:i: $this->i
:j: $this->j
:j1: $this->j1
:j2: $this->j2
:j3: $this->j3
:p1: $this->p1
:p2: $this->p2
:p3: $this->p3
:p6: $this->p6
:q: $this->q
:s1: $this->s1
:s2: $this->s2
:u1: $this->u1
:u2: $this->u2
:v7: $this->v7
:x1: $this->x1
:y1: $this->y1
:y2: $this->y2
:y3: $this->y3
:y4: $this->y4
:y5: $this->y5
:y6: $this->y6
:Formula Number: $this->formulaNumber
:Owner's First Name: $this->ownerFirstName
:Owner's Surname: $this->ownerSurname
:MRZ Line 1: $this->mrz1
:MRZ Line 2: $this->mrz2
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
