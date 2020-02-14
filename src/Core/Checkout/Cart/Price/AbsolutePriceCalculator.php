<?php declare(strict_types=1);

namespace Shopware\Core\Checkout\Cart\Price;

use Shopware\Core\Checkout\Cart\Price\Struct\AbsolutePriceDefinition;
use Shopware\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Shopware\Core\Checkout\Cart\Price\Struct\PriceCollection;
use Shopware\Core\Checkout\Cart\Price\Struct\PriceDefinitionInterface;
use Shopware\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
use Shopware\Core\Checkout\Cart\Tax\PercentageTaxRuleBuilder;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class AbsolutePriceCalculator implements PriceCalculatorInterface
{
    /**
     * @var QuantityPriceCalculator
     */
    private $priceCalculator;

    /**
     * @var PercentageTaxRuleBuilder
     */
    private $percentageTaxRuleBuilder;

    public function __construct(QuantityPriceCalculator $priceCalculator, PercentageTaxRuleBuilder $percentageTaxRuleBuilder)
    {
        $this->priceCalculator = $priceCalculator;
        $this->percentageTaxRuleBuilder = $percentageTaxRuleBuilder;
    }

    public function supports(PriceDefinitionInterface $priceDefinition): bool
    {
        return $priceDefinition instanceof AbsolutePriceDefinition;
    }

    public function calculate(PriceDefinitionInterface $priceDefinition, PriceCollection $prices, SalesChannelContext $context): CalculatedPrice
    {
        if (!($priceDefinition instanceof AbsolutePriceDefinition)) {
            throw new \RuntimeException();
        }

        $taxRules = $this->percentageTaxRuleBuilder->buildRules($prices->sum());

        $priceDefinition = new QuantityPriceDefinition($priceDefinition->getPrice(), $taxRules, $context->getContext()->getCurrencyPrecision(), $priceDefinition->getQuantity(), true);

        return $this->priceCalculator->calculate($priceDefinition, $prices, $context);
    }
}
