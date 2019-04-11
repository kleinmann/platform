<?php declare(strict_types=1);

namespace Shopware\Core\Content\Workflow\Aggregate\WorkflowAction;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                      add(WorkflowActionEntity $entity)
 * @method void                      set(string $key, WorkflowActionEntity $entity)
 * @method WorkflowActionEntity[]    getIterator()
 * @method WorkflowActionEntity[]    getElements()
 * @method WorkflowActionEntity|null get(string $key)
 * @method WorkflowActionEntity|null first()
 * @method WorkflowActionEntity|null last()
 */
class WorkflowActionCollection extends EntityCollection
{
    public function sortByPriority(): void
    {
        $this->sort(function (WorkflowActionEntity $a, WorkflowActionEntity $b) {
            return $b->getPriority() <=> $a->getPriority();
        });
    }

    protected function getExpectedClass(): string
    {
        return WorkflowActionEntity::class;
    }
}
