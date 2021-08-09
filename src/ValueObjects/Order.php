<?php

namespace Davesweb\BrinklinkApi\ValueObjects;

use DateTime;

class Order
{
    public function __construct(
        public ?string $orderId = null,
        public ?DateTime $dateOrdered = null,
        public ?DateTime $dateStatusChanged = null,
        public ?string $sellerName = null,
        public ?string $storeName = null,
        public ?string $buyerName = null,
        public ?string $buyerEmail = null,
        public ?string $buyerOrderCount = null,
        public ?bool $requireInsurance = null,
        public ?string $status = null,
        public ?bool $isInvoiced = null,
        public ?bool $isFiled = null,
        public ?bool $sentDriveThru = null,
        public ?string $remarks,
        public ?int $totalCount = null,
        public ?int $uniqueCount = null,
        public ?float $totalWeight = null,
        public ?Payment $payment = null,
        public ?Shipping $shipping = null,
        public ?Cost $cost = null,
        public ?Cost $dispCost = null,
        public ?iterable $items = null,
        public ?iterable $messages = null,
        public ?iterable $problems = null,
    ) {
    }
}