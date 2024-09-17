<?php

namespace Mindee\Product\NutritionFactsLabel;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\AmountField;

/**
 * Nutrition Facts Label API version 1.0 document data.
 */
class NutritionFactsLabelV1Document extends Prediction
{
    /**
     * @var NutritionFactsLabelV1AddedSugar The amount of added sugars in the product.
     */
    public NutritionFactsLabelV1AddedSugar $addedSugars;
    /**
     * @var NutritionFactsLabelV1Calorie The amount of calories in the product.
     */
    public NutritionFactsLabelV1Calorie $calories;
    /**
     * @var NutritionFactsLabelV1Cholesterol The amount of cholesterol in the product.
     */
    public NutritionFactsLabelV1Cholesterol $cholesterol;
    /**
     * @var NutritionFactsLabelV1DietaryFiber The amount of dietary fiber in the product.
     */
    public NutritionFactsLabelV1DietaryFiber $dietaryFiber;
    /**
     * @var NutritionFactsLabelV1Nutrients The amount of nutrients in the product.
     */
    public NutritionFactsLabelV1Nutrients $nutrients;
    /**
     * @var NutritionFactsLabelV1Protein The amount of protein in the product.
     */
    public NutritionFactsLabelV1Protein $protein;
    /**
     * @var NutritionFactsLabelV1SaturatedFat The amount of saturated fat in the product.
     */
    public NutritionFactsLabelV1SaturatedFat $saturatedFat;
    /**
     * @var AmountField The number of servings in each box of the product.
     */
    public AmountField $servingPerBox;
    /**
     * @var NutritionFactsLabelV1ServingSize The size of a single serving of the product.
     */
    public NutritionFactsLabelV1ServingSize $servingSize;
    /**
     * @var NutritionFactsLabelV1Sodium The amount of sodium in the product.
     */
    public NutritionFactsLabelV1Sodium $sodium;
    /**
     * @var NutritionFactsLabelV1TotalCarbohydrate The total amount of carbohydrates in the product.
     */
    public NutritionFactsLabelV1TotalCarbohydrate $totalCarbohydrate;
    /**
     * @var NutritionFactsLabelV1TotalFat The total amount of fat in the product.
     */
    public NutritionFactsLabelV1TotalFat $totalFat;
    /**
     * @var NutritionFactsLabelV1TotalSugar The total amount of sugars in the product.
     */
    public NutritionFactsLabelV1TotalSugar $totalSugars;
    /**
     * @var NutritionFactsLabelV1TransFat The amount of trans fat in the product.
     */
    public NutritionFactsLabelV1TransFat $transFat;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     * @throws MindeeUnsetException Throws if a field doesn't appear in the response.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        if (!isset($rawPrediction["added_sugars"])) {
            throw new MindeeUnsetException();
        }
        $this->addedSugars = new NutritionFactsLabelV1AddedSugar(
            $rawPrediction["added_sugars"],
            $pageId
        );
        if (!isset($rawPrediction["calories"])) {
            throw new MindeeUnsetException();
        }
        $this->calories = new NutritionFactsLabelV1Calorie(
            $rawPrediction["calories"],
            $pageId
        );
        if (!isset($rawPrediction["cholesterol"])) {
            throw new MindeeUnsetException();
        }
        $this->cholesterol = new NutritionFactsLabelV1Cholesterol(
            $rawPrediction["cholesterol"],
            $pageId
        );
        if (!isset($rawPrediction["dietary_fiber"])) {
            throw new MindeeUnsetException();
        }
        $this->dietaryFiber = new NutritionFactsLabelV1DietaryFiber(
            $rawPrediction["dietary_fiber"],
            $pageId
        );
        if (!isset($rawPrediction["nutrients"])) {
            throw new MindeeUnsetException();
        }
        $this->nutrients = new NutritionFactsLabelV1Nutrients(
            $rawPrediction["nutrients"],
            $pageId
        );
        if (!isset($rawPrediction["protein"])) {
            throw new MindeeUnsetException();
        }
        $this->protein = new NutritionFactsLabelV1Protein(
            $rawPrediction["protein"],
            $pageId
        );
        if (!isset($rawPrediction["saturated_fat"])) {
            throw new MindeeUnsetException();
        }
        $this->saturatedFat = new NutritionFactsLabelV1SaturatedFat(
            $rawPrediction["saturated_fat"],
            $pageId
        );
        if (!isset($rawPrediction["serving_per_box"])) {
            throw new MindeeUnsetException();
        }
        $this->servingPerBox = new AmountField(
            $rawPrediction["serving_per_box"],
            $pageId
        );
        if (!isset($rawPrediction["serving_size"])) {
            throw new MindeeUnsetException();
        }
        $this->servingSize = new NutritionFactsLabelV1ServingSize(
            $rawPrediction["serving_size"],
            $pageId
        );
        if (!isset($rawPrediction["sodium"])) {
            throw new MindeeUnsetException();
        }
        $this->sodium = new NutritionFactsLabelV1Sodium(
            $rawPrediction["sodium"],
            $pageId
        );
        if (!isset($rawPrediction["total_carbohydrate"])) {
            throw new MindeeUnsetException();
        }
        $this->totalCarbohydrate = new NutritionFactsLabelV1TotalCarbohydrate(
            $rawPrediction["total_carbohydrate"],
            $pageId
        );
        if (!isset($rawPrediction["total_fat"])) {
            throw new MindeeUnsetException();
        }
        $this->totalFat = new NutritionFactsLabelV1TotalFat(
            $rawPrediction["total_fat"],
            $pageId
        );
        if (!isset($rawPrediction["total_sugars"])) {
            throw new MindeeUnsetException();
        }
        $this->totalSugars = new NutritionFactsLabelV1TotalSugar(
            $rawPrediction["total_sugars"],
            $pageId
        );
        if (!isset($rawPrediction["trans_fat"])) {
            throw new MindeeUnsetException();
        }
        $this->transFat = new NutritionFactsLabelV1TransFat(
            $rawPrediction["trans_fat"],
            $pageId
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $servingSizeToFieldList = $this->servingSize != null ? $this->servingSize->toFieldList() : "";
        $caloriesToFieldList = $this->calories != null ? $this->calories->toFieldList() : "";
        $totalFatToFieldList = $this->totalFat != null ? $this->totalFat->toFieldList() : "";
        $saturatedFatToFieldList = $this->saturatedFat != null ? $this->saturatedFat->toFieldList() : "";
        $transFatToFieldList = $this->transFat != null ? $this->transFat->toFieldList() : "";
        $cholesterolToFieldList = $this->cholesterol != null ? $this->cholesterol->toFieldList() : "";
        $totalCarbohydrateToFieldList = $this->totalCarbohydrate != null ? $this->totalCarbohydrate->toFieldList() : "";
        $dietaryFiberToFieldList = $this->dietaryFiber != null ? $this->dietaryFiber->toFieldList() : "";
        $totalSugarsToFieldList = $this->totalSugars != null ? $this->totalSugars->toFieldList() : "";
        $addedSugarsToFieldList = $this->addedSugars != null ? $this->addedSugars->toFieldList() : "";
        $proteinToFieldList = $this->protein != null ? $this->protein->toFieldList() : "";
        $sodiumToFieldList = $this->sodium != null ? $this->sodium->toFieldList() : "";
        $nutrientsSummary = strval($this->nutrients);

        $outStr = ":Serving per Box: $this->servingPerBox
:Serving Size: $servingSizeToFieldList
:Calories: $caloriesToFieldList
:Total Fat: $totalFatToFieldList
:Saturated Fat: $saturatedFatToFieldList
:Trans Fat: $transFatToFieldList
:Cholesterol: $cholesterolToFieldList
:Total Carbohydrate: $totalCarbohydrateToFieldList
:Dietary Fiber: $dietaryFiberToFieldList
:Total Sugars: $totalSugarsToFieldList
:Added Sugars: $addedSugarsToFieldList
:Protein: $proteinToFieldList
:sodium: $sodiumToFieldList
:nutrients: $nutrientsSummary
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
