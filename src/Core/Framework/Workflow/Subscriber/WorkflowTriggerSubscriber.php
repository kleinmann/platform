<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Workflow\Subscriber;

use Shopware\Core\Checkout\Customer\Event\CustomerRegistrationEvent;
use Shopware\Core\Checkout\Order\Event\OrderEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Event\BusinessEvents;
use Shopware\Core\Framework\Struct\StructCollection;
use Shopware\Core\Framework\Workflow\WorkflowService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WorkflowTriggerSubscriber implements EventSubscriberInterface
{
    public const TRIGGER_CUSTOMER_REGISTRATION = 'customer.registration';
    public const TRIGGER_ORDER = 'order';

    public const TRIGGERS = [
        self::TRIGGER_CUSTOMER_REGISTRATION,
        self::TRIGGER_ORDER,
    ];

    /**
     * @var WorkflowService
     */
    private $workflowService;

    /**
     * @var EntityRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $orderRepository;

    public function __construct(WorkflowService $workflowService, EntityRepositoryInterface $customerRepository, EntityRepositoryInterface $orderRepository)
    {
        $this->workflowService = $workflowService;
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BusinessEvents::CHECKOUT_CUSTOMER_REGISTRATION => [
                ['onCustomerRegistration'],
            ],
            BusinessEvents::CHECKOUT_ORDER => [
                ['onOrder'],
            ],
        ];
    }

    public function onCustomerRegistration(CustomerRegistrationEvent $event)
    {
        $data = new StructCollection();
        $data->set('customer', $this->customerRepository->search(new Criteria([$event->getCustomerId()]), $event->getContext())->first());

        $this->workflowService->executeForTrigger(self::TRIGGER_CUSTOMER_REGISTRATION, $event->getSalesChannelContext(), $data);
    }

    public function onOrder(OrderEvent $event)
    {
        $data = new StructCollection();
        $data->set('order', $this->orderRepository->search(new Criteria([$event->getOrderId()]), $event->getContext())->first());

        $this->workflowService->executeForTrigger(self::TRIGGER_ORDER, $event->getSalesChannelContext(), $data);
    }
}
