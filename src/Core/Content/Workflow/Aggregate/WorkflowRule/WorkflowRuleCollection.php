<?php declare(strict_types=1);

namespace Shopware\Core\Content\Workflow\Aggregate\WorkflowRule;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                    add(WorkflowRuleEntity $entity)
 * @method void                    set(string $key, WorkflowRuleEntity $entity)
 * @method WorkflowRuleEntity[]    getIterator()
 * @method WorkflowRuleEntity[]    getElements()
 * @method WorkflowRuleEntity|null get(string $key)
 * @method WorkflowRuleEntity|null first()
 * @method WorkflowRuleEntity|null last()
 */
class WorkflowRuleCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return WorkflowRuleEntity::class;
    }
}
