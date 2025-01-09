<?php

namespace Mindee\Product\Us\UsMail;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\BooleanField;
use Mindee\Parsing\Standard\StringField;

/**
 * US Mail API version 3.0 document data.
 */
class UsMailV3Document extends Prediction
{
    /**
     * @var BooleanField Whether the mailing is marked as return to sender.
     */
    public BooleanField $isReturnToSender;
    /**
     * @var UsMailV3RecipientAddresses The addresses of the recipients.
     */
    public UsMailV3RecipientAddresses $recipientAddresses;
    /**
     * @var StringField[] The names of the recipients.
     */
    public array $recipientNames;
    /**
     * @var UsMailV3SenderAddress The address of the sender.
     */
    public UsMailV3SenderAddress $senderAddress;
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
        if (!isset($rawPrediction["is_return_to_sender"])) {
            throw new MindeeUnsetException();
        }
        $this->isReturnToSender = new BooleanField(
            $rawPrediction["is_return_to_sender"],
            $pageId
        );
        if (!isset($rawPrediction["recipient_addresses"])) {
            throw new MindeeUnsetException();
        }
        $this->recipientAddresses = new UsMailV3RecipientAddresses(
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
        $this->senderAddress = new UsMailV3SenderAddress(
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
:Return to Sender: $this->isReturnToSender
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
