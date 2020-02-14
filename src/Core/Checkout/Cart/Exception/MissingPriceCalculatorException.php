<?php declare(strict_types=1);

namespace Shopware\Core\Checkout\Cart\Exception;

use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

class MissingPriceCalculatorException extends ShopwareHttpException
{
    public function __construct(string $type)
    {
        parent::__construct(
            'Price definition of type {{ type }} has no calculator able to process it.',
            ['type' => $type]
        );
    }

    public function getErrorCode(): string
    {
        return 'CHECKOUT__CART_MISSING_PRICE_CALCULATOR';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_CONFLICT;
    }
}
