<?php declare(strict_types=1);

namespace Shopware\Core\Content\Workflow\Aggregate\WorkflowAction;

use Shopware\Core\Content\Workflow\WorkflowDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
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
            (new FkField('workflow_id', 'workflowId', WorkflowDefinition::class))->addFlags(new Required()),
            (new StringField('handler_identifier', 'handlerIdentifier'))->addFlags(new Required()),
            new CreatedAtField(),
            new UpdatedAtField(),
            (new JsonField('configuration', 'configuration'))->addFlags(new Required()),

            new ManyToOneAssociationField('workflow', 'workflow_id', WorkflowDefinition::class),
        ]);
    }
}
