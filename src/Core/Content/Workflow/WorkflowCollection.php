<?php declare(strict_types=1);

namespace Shopware\Core\Content\Workflow;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                add(WorkflowEntity $entity)
 * @method void                set(string $key, WorkflowEntity $entity)
 * @method WorkflowEntity[]    getIterator()
 * @method WorkflowEntity[]    getElements()
 * @method WorkflowEntity|null get(string $key)
 * @method WorkflowEntity|null first()
 * @method WorkflowEntity|null last()
 */
class WorkflowCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return WorkflowEntity::class;
    }
}
