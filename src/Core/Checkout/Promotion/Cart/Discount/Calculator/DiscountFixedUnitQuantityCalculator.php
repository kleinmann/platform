<?php declare(strict_types=1);

namespace Shopware\Core\Checkout\Promotion\Cart\Discount\Calculator;

use Shopware\Core\Checkout\Cart\Price\AbsolutePriceCalculator;
use Shopware\Core\Checkout\Cart\Price\Struct\AbsolutePriceDefinition;
use Shopware\Core\Checkout\Promotion\Cart\Discount\Composition\DiscountCompositionItem;
use Shopware\Core\Checkout\Promotion\Cart\Discount\DiscountCalculatorResult;
use Shopware\Core\Checkout\Promotion\Cart\Discount\DiscountLineItem;
use Shopware\Core\Checkout\Promotion\Cart\Discount\DiscountPackageCollection;
use Shopware\Core\Checkout\Promotion\Exception\InvalidPriceDefinitionException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class DiscountFixedUnitQuantityCalculator
{
    /**
     * @var AbsolutePriceCalculator
     */
    private $absolutePriceCalculator;

    public function __construct(AbsolutePriceCalculator $absolutePriceCalculator)
    {
        $this->absolutePriceCalculator = $absolutePriceCalculator;
    }

    /**
     * @throws InvalidPriceDefinitionException
     */
    public function calculate(DiscountLineItem $discount, DiscountPackageCollection $packages, SalesChannelContext $context): DiscountCalculatorResult
    {
        /** @var AbsolutePriceDefinition|null $priceDefinition */
        $priceDefinition = $discount->getPriceDefinition();

        if (!$priceDefinition instanceof AbsolutePriceDefinition) {
            throw new InvalidPriceDefinitionException($discount->getLabel(), $discount->getCode());
        }

        $discountQuantity = (int) $priceDefinition->getPrice();

        $totalDiscountSum = 0.0;

        $composition = [];

        foreach ($packages as $package) {
            $i = 0;

            foreach ($package->getCartItems() as $lineItem) {
                if ($i >= $discountQuantity) {
                    break;
                }

                $quantity = $lineItem->getQuantity();
                $itemUnitPrice = $lineItem->getPrice()->getUnitPrice();

                // add to our total discount sum
                $lineItemDiscountQuantity = min($discountQuantity, $quantity);
                $discountDiffPrice = $itemUnitPrice * $lineItemDiscountQuantity;
                $totalDiscountSum += $discountDiffPrice;
                $i += $lineItemDiscountQuantity;

                // add a reference, so we know what items are discounted
                $composition[] = new DiscountCompositionItem($lineItem->getId(), $lineItemDiscountQuantity, $discountDiffPrice);
            }
        }

        // now calculate the correct price
        // from our collected total discount price
        $discountPrice = $this->absolutePriceCalculator->calculate(
            -abs($totalDiscountSum),
            $packages->getAffectedPrices(),
            $context
        );

        return new DiscountCalculatorResult($discountPrice, $composition);
    }
}
