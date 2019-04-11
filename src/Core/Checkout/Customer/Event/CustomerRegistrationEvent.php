<?php declare(strict_types=1);

namespace Shopware\Core\Checkout\Customer\Event;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\BusinessEventInterface;
use Shopware\Core\Framework\Event\EventData\EventDataCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\EventDispatcher\Event;

class CustomerRegistrationEvent extends Event implements BusinessEventInterface
{
    public const EVENT_NAME = 'checkout.customer.registration';

    /**
     * @var string
     */
    private $customerId;

    /**
     * @var SalesChannelContext
     */
    private $context;

    public function __construct(SalesChannelContext $context, string $customerId)
    {
        $this->customerId = $customerId;
        $this->context = $context;
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

    public function getCustomerId(): string
    {
        return $this->customerId;
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
