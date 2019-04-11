<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Event;

use Shopware\Core\Checkout\Customer\Event\CustomerLoginEvent;
use Shopware\Core\Checkout\Customer\Event\CustomerLogoutEvent;
use Shopware\Core\Checkout\Customer\Event\CustomerRegistrationEvent;
use Shopware\Core\Checkout\Order\Event\OrderEvent;
use Shopware\Core\System\User\Recovery\UserRecoveryRequestEvent;

final class BusinessEvents
{
    /**
     * @Event("Shopware\Core\Framework\Event\BusinessEvent")
     */
    public const GLOBAL_EVENT = 'shopware.global_business_event';

    /**
     * @Event("Shopware\Core\Checkout\Customer\Event\CustomerLoginEvent")
     */
    public const CHECKOUT_CUSTOMER_LOGIN = CustomerLoginEvent::EVENT_NAME;

    /**
     * @Event("Shopware\Core\Checkout\Customer\Event\CustomerLogoutEvent")
     */
    public const CHECKOUT_CUSTOMER_LOGOUT = CustomerLogoutEvent::EVENT_NAME;

    /**
     * @Event("Shopware\Core\Checkout\Customer\Event\CustomerRegistrationEvent")
     */
    public const CHECKOUT_CUSTOMER_REGISTRATION = CustomerRegistrationEvent::EVENT_NAME;

    /**
     * @Event("Shopware\Core\Checkout\Order\Event\OrderEvent")
     */
    public const CHECKOUT_ORDER = OrderEvent::EVENT_NAME;

    /**
     * @Event("Shopware\Core\System\User\Recovery\UserRecoveryRequestEvent")
     */
    public const USER_RECOVERY_REQUEST = UserRecoveryRequestEvent::EVENT_NAME;

    private function __construct()
    {
    }
}
