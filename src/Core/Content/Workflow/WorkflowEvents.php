<?php declare(strict_types=1);

namespace Shopware\Core\Content\Workflow;

class WorkflowEvents
{
    /**
     * @Event("Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    public const WORKFLOW_ACTION_LOADED_EVENT = 'workflow_action.loaded';
}
