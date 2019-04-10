<?php declare(strict_types=1);

namespace Shopware\Core\Content\Workflow;

use Shopware\Core\Content\Workflow\Aggregate\WorkflowRule\WorkflowRuleDefinition;
use Shopware\Core\Content\Workflow\Aggregate\WorkflowWorkflowAction\WorkflowWorkflowActionDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\AttributesField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class WorkflowDefinition extends EntityDefinition
{
    public static function getEntityName(): string
    {
        return 'workflow';
    }

    public static function getCollectionClass(): string
    {
        return WorkflowCollection::class;
    }

    public static function getEntityClass(): string
    {
        return WorkflowEntity::class;
    }

    protected static function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new StringField('trigger', 'trigger'))->addFlags(new Required()),
            (new StringField('name', 'name'))->addFlags(new Required()),
            new AttributesField(),
            new CreatedAtField(),
            new UpdatedAtField(),

            (new OneToManyAssociationField('workflowRules', WorkflowRuleDefinition::class, 'workflow_id'))->addFlags(new CascadeDelete()),
            (new OneToManyAssociationField('workflowWorkflowActions', WorkflowWorkflowActionDefinition::class, 'workflow_id'))->addFlags(new CascadeDelete()),
        ]);
    }
}
