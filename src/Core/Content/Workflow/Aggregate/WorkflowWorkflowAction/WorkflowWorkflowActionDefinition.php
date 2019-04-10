<?php declare(strict_types=1);

namespace Shopware\Core\Content\Workflow\Aggregate\WorkflowWorkflowAction;

use Shopware\Core\Content\Workflow\Aggregate\WorkflowAction\WorkflowActionDefinition;
use Shopware\Core\Content\Workflow\WorkflowDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;

class WorkflowWorkflowActionDefinition extends MappingEntityDefinition
{
    public static function getEntityName(): string
    {
        return 'workflow_workflow_action';
    }

    public static function getCollectionClass(): string
    {
        return WorkflowWorkflowActionCollection::class;
    }

    public static function getEntityClass(): string
    {
        return WorkflowWorkflowActionEntity::class;
    }

    protected static function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('workflow_id', 'workflowId', WorkflowDefinition::class))->addFlags(new Required()),
            (new FkField('workflow_action_id', 'workflowActionId', WorkflowActionDefinition::class))->addFlags(new Required()),
            new CreatedAtField(),
            new UpdatedAtField(),
            (new JsonField('configuration', 'configuration'))->addFlags(new Required()),

            new ManyToOneAssociationField('workflow', 'workflow_id', WorkflowDefinition::class),
            new ManyToOneAssociationField('workflowAction', 'workflow_action_id', WorkflowActionDefinition::class),
        ]);
    }
}
