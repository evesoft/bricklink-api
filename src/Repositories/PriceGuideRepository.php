<?php

namespace Davesweb\BrinklinkApi\Repositories;

use function Davesweb\uri;
use Davesweb\BrinklinkApi\ValueObjects\PriceGuide;
use Davesweb\BrinklinkApi\Contracts\BricklinkGateway;
use Davesweb\BrinklinkApi\Exceptions\NotFoundException;
use Davesweb\BrinklinkApi\Transformers\PriceGuideTransformer;

class PriceGuideRepository extends BaseRepository
{
    public function __construct(BricklinkGateway $gateway, PriceGuideTransformer $transformer)
    {
        parent::__construct($gateway, $transformer);
    }

    public function find(
        string $number,
        string $type = 'part',
        ?int $colorId = null,
        string $guideType = 'stock',
        string $newOrUsed = 'N',
        ?string $countryCode = null,
        ?string $region = null,
        ?string $currencyCode = null,
        string $vat = 'N',
    ): ?PriceGuide {
        $uri = uri('/items/{type}/{number}/price', [
            'type'   => $type,
            'number' => $number,
        ], [
            'color_id'      => $colorId,
            'guide_type'    => $guideType,
            'new_or_used'   => $newOrUsed,
            'country_code'  => $countryCode,
            'region'        => $region,
            'currency_code' => $currencyCode,
            'vat'           => $vat,
        ]);

        $response = $this->gateway->get($uri);

        if (!$response->hasData()) {
            return null;
        }

        /** @var PriceGuide $priceGuide */
        $priceGuide = $this->transformer->toObject($response->getData());

        return $priceGuide;
    }

    public function findOrFail(
        string $number,
        string $type = 'part',
        ?int $colorId = null,
        string $guideType = 'stock',
        string $newOrUsed = 'N',
        ?string $countryCode = null,
        ?string $region = null,
        ?string $currencyCode = null,
        string $vat = 'N',
    ): PriceGuide {
        $priceGuide = $this->find($number, $type, $colorId, $guideType, $newOrUsed, $countryCode, $region, $currencyCode, $vat);

        if (null === $priceGuide) {
            throw NotFoundException::forId($number);
        }

        return $priceGuide;
    }
}