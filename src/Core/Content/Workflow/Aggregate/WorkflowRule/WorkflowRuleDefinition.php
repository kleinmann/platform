<?php declare(strict_types=1);

namespace Shopware\Core\Content\Workflow\Aggregate\WorkflowRule;

use Shopware\Core\Content\Rule\RuleDefinition;
use Shopware\Core\Content\Workflow\WorkflowDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;

class WorkflowRuleDefinition extends MappingEntityDefinition
{
    public static function getEntityName(): string
    {
        return 'workflow_rule';
    }

    public static function getCollectionClass(): string
    {
        return WorkflowRuleCollection::class;
    }

    public static function getEntityClass(): string
    {
        return WorkflowRuleEntity::class;
    }

    protected static function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('workflow_id', 'workflowId', WorkflowDefinition::class))->addFlags((new Required())),
            (new FkField('rule_id', 'ruleId', RuleDefinition::class))->addFlags(new Required()),
            new CreatedAtField(),
            new UpdatedAtField(),

            new ManyToOneAssociationField('workflow', 'workflow_id', WorkflowDefinition::class),
            new ManyToOneAssociationField('rule', 'rule_id', RuleDefinition::class),
        ]);
    }
}
