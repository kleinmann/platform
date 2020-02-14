<?php declare(strict_types=1);

namespace Shopware\Core\Checkout\Cart\Price;

use Shopware\Core\Checkout\Cart\Exception\MissingPriceCalculatorException;
use Shopware\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Shopware\Core\Checkout\Cart\Price\Struct\PriceCollection;
use Shopware\Core\Checkout\Cart\Price\Struct\PriceDefinitionInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class Calculator implements CalculatorInterface
{
    /**
     * @var iterable|PriceCalculatorInterface[]
     */
    private $calculators;

    public function __construct(
        iterable $calculators
    ) {
        $this->calculators = $calculators;
    }

    public function calculatePriceDefinition(PriceDefinitionInterface $definition, PriceCollection $prices, SalesChannelContext $context): CalculatedPrice
    {
        foreach ($this->calculators as $calculator) {
            if ($calculator->supports($definition)) {
                return $calculator->calculate($definition, $prices, $context);
            }
        }

        throw new MissingPriceCalculatorException(get_class($definition));
    }
}
