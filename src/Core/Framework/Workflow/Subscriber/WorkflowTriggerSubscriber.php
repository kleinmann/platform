<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Workflow\Subscriber;

use function Flag\next1309;
use Shopware\Core\Checkout\Customer\Event\CustomerRegistrationEvent;
use Shopware\Core\Checkout\Order\Event\OrderEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Event\BusinessEvents;
use Shopware\Core\Framework\Struct\StructCollection;
use Shopware\Core\Framework\Workflow\Message\ActionMessage;
use Shopware\Core\Framework\Workflow\WorkflowService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class WorkflowTriggerSubscriber implements EventSubscriberInterface
{
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

    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    public function __construct(
        WorkflowService $workflowService,
        EntityRepositoryInterface $customerRepository,
        EntityRepositoryInterface $orderRepository,
        MessageBusInterface $messageBus)
    {
        $this->workflowService = $workflowService;
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
        $this->messageBus = $messageBus;
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

        if (next1309()) {
            $message = new ActionMessage(BusinessEvents::CHECKOUT_CUSTOMER_REGISTRATION, $data, $event->getSalesChannelContext());
            $this->messageBus->dispatch($message);

            return;
        }

        $this->workflowService->executeForTrigger(BusinessEvents::CHECKOUT_CUSTOMER_REGISTRATION, $event->getSalesChannelContext(), $data);
    }

    public function onOrder(OrderEvent $event)
    {
        $data = new StructCollection();
        $data->set('order', $this->orderRepository->search(new Criteria([$event->getOrderId()]), $event->getContext())->first());

        if (next1309()) {
            $message = new ActionMessage(BusinessEvents::CHECKOUT_ORDER, $data, $event->getSalesChannelContext());
            $this->messageBus->dispatch($message);

            return;
        }

        $this->workflowService->executeForTrigger(BusinessEvents::CHECKOUT_CUSTOMER_REGISTRATION, $event->getSalesChannelContext(), $data);
    }
}
