<?php declare(strict_types=1);

namespace Shopware\Core\Content\Workflow\Aggregate\WorkflowAction\Subscriber;

use Shopware\Core\Content\Workflow\Aggregate\WorkflowAction\WorkflowActionEntity;
use Shopware\Core\Content\Workflow\WorkflowEvents;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Shopware\Core\Framework\Workflow\ActionProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WorkflowActionLoadedSubscriber implements EventSubscriberInterface
{
    /**
     * @var ActionProvider
     */
    private $actionProvider;

    public function __construct(ActionProvider $actionProvider)
    {
        $this->actionProvider = $actionProvider;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkflowEvents::WORKFLOW_ACTION_LOADED_EVENT => [
                ['loadFields'],
            ],
        ];
    }

    public function loadFields(EntityLoadedEvent $event): void
    {
        /** @var WorkflowActionEntity $entity */
        foreach ($event->getEntities() as $entity) {
            $action = $this->actionProvider->getActionForHandlerIdentifier($entity->getHandlerIdentifier());
            $entity->setFields($action->getFields());
        }
    }
}
