<?php declare(strict_types=1);

namespace Shopware\Core\Content\Workflow\Aggregate\WorkflowWorkflowAction;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                              add(WorkflowWorkflowActionEntity $entity)
 * @method void                              set(string $key, WorkflowWorkflowActionEntity $entity)
 * @method WorkflowWorkflowActionEntity[]    getIterator()
 * @method WorkflowWorkflowActionEntity[]    getElements()
 * @method WorkflowWorkflowActionEntity|null get(string $key)
 * @method WorkflowWorkflowActionEntity|null first()
 * @method WorkflowWorkflowActionEntity|null last()
 */
class WorkflowWorkflowActionCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return WorkflowWorkflowActionEntity::class;
    }
}
