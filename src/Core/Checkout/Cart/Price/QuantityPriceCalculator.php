<?php declare(strict_types=1);

namespace Shopware\Core\Checkout\Cart\Price;

use Shopware\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Shopware\Core\Checkout\Cart\Price\Struct\PriceCollection;
use Shopware\Core\Checkout\Cart\Price\Struct\PriceDefinitionInterface;
use Shopware\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
use Shopware\Core\Checkout\Cart\Tax\Struct\CalculatedTaxCollection;
use Shopware\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;
use Shopware\Core\Checkout\Cart\Tax\TaxDetector;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class QuantityPriceCalculator implements PriceCalculatorInterface
{
    /**
     * @var GrossPriceCalculator
     */
    private $grossPriceCalculator;

    /**
     * @var NetPriceCalculator
     */
    private $netPriceCalculator;

    /**
     * @var TaxDetector
     */
    private $taxDetector;

    /**
     * @var ReferencePriceCalculator
     */
    private $referencePriceCalculator;

    public function __construct(
        GrossPriceCalculator $grossPriceCalculator,
        NetPriceCalculator $netPriceCalculator,
        TaxDetector $taxDetector,
        ReferencePriceCalculator $referencePriceCalculator
    ) {
        $this->grossPriceCalculator = $grossPriceCalculator;
        $this->netPriceCalculator = $netPriceCalculator;
        $this->taxDetector = $taxDetector;
        $this->referencePriceCalculator = $referencePriceCalculator;
    }

    public function supports(PriceDefinitionInterface $priceDefinition): bool
    {
        return $priceDefinition instanceof QuantityPriceDefinition;
    }

    public function calculate(PriceDefinitionInterface $priceDefinition, PriceCollection $prices, SalesChannelContext $context): CalculatedPrice
    {
        if (!($priceDefinition instanceof QuantityPriceDefinition)) {
            throw new \RuntimeException();
        }

        if ($this->taxDetector->useGross($context)) {
            $price = $this->grossPriceCalculator->calculate($priceDefinition);
        } else {
            $price = $this->netPriceCalculator->calculate($priceDefinition);
        }

        $taxRules = $price->getTaxRules();
        $calculatedTaxes = $price->getCalculatedTaxes();

        if ($this->taxDetector->isNetDelivery($context)) {
            $taxRules = new TaxRuleCollection();
            $calculatedTaxes = new CalculatedTaxCollection();
        }

        return new CalculatedPrice(
            $price->getUnitPrice(),
            $price->getTotalPrice(),
            $calculatedTaxes,
            $taxRules,
            $price->getQuantity(),
            $this->referencePriceCalculator->calculate($price->getUnitPrice(), $priceDefinition),
            $price->getListPrice()
        );
    }
}
