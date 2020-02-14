<?php declare(strict_types=1);

namespace Shopware\Core\Checkout\Cart\Price;

use Shopware\Core\Checkout\Cart\Exception\MissingPriceCalculatorException;
use Shopware\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Shopware\Core\Checkout\Cart\Price\Struct\PriceCollection;
use Shopware\Core\Checkout\Cart\Price\Struct\PriceDefinitionInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

interface CalculatorInterface
{
    /**
     * @throws MissingPriceCalculatorException
     */
    public function calculatePriceDefinition(PriceDefinitionInterface $definition, PriceCollection $prices, SalesChannelContext $context): CalculatedPrice;
}
