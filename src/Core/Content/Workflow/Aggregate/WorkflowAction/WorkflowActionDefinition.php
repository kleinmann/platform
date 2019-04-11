<?php declare(strict_types=1);

namespace Shopware\Core\Content\Workflow\Aggregate\WorkflowAction;

use Shopware\Core\Content\Workflow\Aggregate\WorkflowWorkflowAction\WorkflowWorkflowActionDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\AttributesField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Deferred;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\WriteProtected;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class WorkflowActionDefinition extends EntityDefinition
{
    public static function getEntityName(): string
    {
        return 'workflow_action';
    }

    public static function getCollectionClass(): string
    {
        return WorkflowActionCollection::class;
    }

    public static function getEntityClass(): string
    {
        return WorkflowActionEntity::class;
    }

    protected static function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new StringField('name', 'name'))->addFlags(new Required()),
            (new StringField('handler_identifier', 'handlerIdentifier'))->addFlags(new Required(), new WriteProtected()),
            new AttributesField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            (new JsonField('fields', 'fields'))->addFlags(new Deferred()),

            (new OneToManyAssociationField('workflowWorkflowActions', WorkflowWorkflowActionDefinition::class, 'workflow_action_id'))->addFlags(new CascadeDelete()),
        ]);
    }
}
