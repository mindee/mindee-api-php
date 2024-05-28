<?php

namespace Mindee\Product\Us\UsMail;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\StringField;

/**
 * US Mail API version 2.0 document data.
 */
class UsMailV2Document extends Prediction
{
    /**
     * @var UsMailV2RecipientAddresses The addresses of the recipients.
     */
    public UsMailV2RecipientAddresses $recipientAddresses;
    /**
     * @var StringField[] The names of the recipients.
     */
    public array $recipientNames;
    /**
     * @var UsMailV2SenderAddress The address of the sender.
     */
    public UsMailV2SenderAddress $senderAddress;
    /**
     * @var StringField The name of the sender.
     */
    public StringField $senderName;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     * @throws MindeeUnsetException Throws if a field doesn't appear in the response.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        if (!isset($rawPrediction["recipient_addresses"])) {
            throw new MindeeUnsetException();
        }
        $this->recipientAddresses = new UsMailV2RecipientAddresses(
            $rawPrediction["recipient_addresses"],
            $pageId
        );
        if (!isset($rawPrediction["recipient_names"])) {
            throw new MindeeUnsetException();
        }
        $this->recipientNames = $rawPrediction["recipient_names"] == null ? [] : array_map(
            fn ($prediction) => new StringField($prediction, $pageId),
            $rawPrediction["recipient_names"]
        );
        if (!isset($rawPrediction["sender_address"])) {
            throw new MindeeUnsetException();
        }
        $this->senderAddress = new UsMailV2SenderAddress(
            $rawPrediction["sender_address"],
            $pageId
        );
        if (!isset($rawPrediction["sender_name"])) {
            throw new MindeeUnsetException();
        }
        $this->senderName = new StringField(
            $rawPrediction["sender_name"],
            $pageId
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $senderAddressToFieldList = $this->senderAddress != null ? $this->senderAddress->toFieldList() : "";
        $recipientNames = implode(
            "\n                  ",
            $this->recipientNames
        );
        $recipientAddressesSummary = strval($this->recipientAddresses);

        $outStr = ":Sender Name: $this->senderName
:Sender Address: $senderAddressToFieldList
:Recipient Names: $recipientNames
:Recipient Addresses: $recipientAddressesSummary
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
