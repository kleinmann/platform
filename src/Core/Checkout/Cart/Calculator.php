<?php declare(strict_types=1);

namespace Shopware\Core\Checkout\Cart;

use Shopware\Core\Checkout\Cart\Exception\MissingLineItemPriceException;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\LineItem\LineItemCollection;
use Shopware\Core\Checkout\Cart\Price\CalculatorInterface;
use Shopware\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Shopware\Core\Checkout\Cart\Rule\LineItemScope;
use Shopware\Core\Framework\Rule\Rule;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class Calculator
{
    /**
     * @var Calculator
     */
    private $calculator;

    public function __construct(CalculatorInterface $calculator)
    {
        $this->calculator = $calculator;
    }

    public function calculate(LineItemCollection $lineItems, SalesChannelContext $context, CartBehavior $behavior): LineItemCollection
    {
        return $this->calculateLineItems($lineItems, $context, $behavior);
    }

    private function calculateLineItems(LineItemCollection $lineItems, SalesChannelContext $context, CartBehavior $behavior): LineItemCollection
    {
        $workingSet = clone $lineItems;
        $workingSet->sortByPriority();

        $calculated = new LineItemCollection();

        foreach ($workingSet as $original) {
            $lineItem = LineItem::createFromLineItem($original);

            $price = $this->calculatePrice($lineItem, $context, $calculated, $behavior);

            $lineItem->setPrice($price);

            $calculated->add($lineItem);
        }

        return $calculated;
    }

    private function filterLineItems(LineItemCollection $calculated, ?Rule $filter, SalesChannelContext $context): LineItemCollection
    {
        if (!$filter) {
            return $calculated;
        }

        return $calculated->filter(
            function (LineItem $lineItem) use ($filter, $context) {
                $match = $filter->match(
                    new LineItemScope($lineItem, $context)
                );

                return $match;
            }
        );
    }

    private function calculatePrice(LineItem $lineItem, SalesChannelContext $context, LineItemCollection $calculated, CartBehavior $behavior): CalculatedPrice
    {
        if ($lineItem->hasChildren()) {
            $children = $this->calculateLineItems($lineItem->getChildren(), $context, $behavior);

            $lineItem->setChildren($children);

            return $children->getPrices()->sum();
        }

        $definition = $lineItem->getPriceDefinition();

        if (!$definition) {
            throw new MissingLineItemPriceException($lineItem->getId());
        }

        //reduce line items for provided filter
        $prices = $this->filterLineItems($calculated, $definition->getFilter(), $context)
            ->getPrices();

        return $this->calculator->calculatePriceDefinition($definition, $prices, $context);
    }
}
