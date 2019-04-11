<?php declare(strict_types=1);

namespace Shopware\Core\Checkout\Order\Event;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\BusinessEventInterface;
use Shopware\Core\Framework\Event\EventData\EventDataCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\EventDispatcher\Event;

class OrderEvent extends Event implements BusinessEventInterface
{
    public const EVENT_NAME = 'checkout.order';

    /**
     * @var string
     */
    private $orderId;

    /**
     * @var SalesChannelContext
     */
    private $context;

    public function __construct(SalesChannelContext $context, string $orderId)
    {
        $this->context = $context;
        $this->orderId = $orderId;
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function getContext(): Context
    {
        return $this->context->getContext();
    }

    public function getSalesChannelContext(): SalesChannelContext
    {
        return $this->context;
    }

    public static function getAvailableData(): EventDataCollection
    {
        return new EventDataCollection();
    }
}
