<?php declare(strict_types=1);

namespace Shopware\Core\Checkout\Cart;

use Shopware\Core\Checkout\Cart\Exception\CartTokenNotFoundException;
use Shopware\Core\Checkout\Cart\Rule\CartRuleScope;
use Shopware\Core\Content\Rule\RuleCollection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Rule\Evaluator;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class CartRuleLoader
{
    private const MAX_ITERATION = 5;

    /**
     * @var CartPersisterInterface
     */
    private $cartPersister;

    /**
     * @var Processor
     */
    private $processor;

    /**
     * @var Evaluator
     */
    private $evaluator;

    public function __construct(
        CartPersisterInterface $cartPersister,
        Processor $processor,
        Evaluator $evaluator
    ) {
        $this->cartPersister = $cartPersister;
        $this->processor = $processor;
        $this->evaluator = $evaluator;
    }

    public function loadByToken(SalesChannelContext $context, string $cartToken): RuleLoaderResult
    {
        try {
            $cart = $this->cartPersister->load($cartToken, $context);
        } catch (CartTokenNotFoundException $e) {
            $cart = new Cart($context->getSalesChannel()->getTypeId(), $cartToken);
        }

        return $this->loadByCart($context, $cart, new CartBehavior());
    }

    public function loadByCart(SalesChannelContext $context, Cart $cart, CartBehavior $behaviorContext): RuleLoaderResult
    {
        return $this->load($context, $cart, $behaviorContext);
    }

    private function load(SalesChannelContext $context, Cart $cart, CartBehavior $behaviorContext): RuleLoaderResult
    {
        $iteration = 1;
        $rules = new RuleCollection([]);

        $valid = true;
        do {
            if ($iteration > self::MAX_ITERATION) {
                break;
            }

            $rules = $this->evaluator->getMatchingRules(new CartRuleScope($cart, $context), $context->getContext());

            //place rules into context for further usages
            $context->setRuleIds($rules->getIds());

            //recalculate cart for new context rules
            $new = $this->processor->process($cart, $context, $behaviorContext);

            if ($this->cartChanged($cart, $new)) {
                $valid = false;
            }

            $cart = $new;

            ++$iteration;
        } while ($valid);

        $context->setRuleIds($rules->getIds());

        return new RuleLoaderResult($cart, $rules);
    }

    private function cartChanged(Cart $previous, Cart $current): bool
    {
        $previousLineItems = $previous->getLineItems();
        $currentLineItems = $current->getLineItems();

        return $previousLineItems->count() !== $currentLineItems->count()
            || $previous->getPrice()->getTotalPrice() !== $current->getPrice()->getTotalPrice()
            || $previousLineItems->getKeys() !== $currentLineItems->getKeys()
            || $previousLineItems->getTypes() !== $currentLineItems->getTypes()
        ;
    }
}
