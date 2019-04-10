<?php declare(strict_types=1);

namespace Shopware\Core\Content\Workflow\Aggregate\WorkflowRule;

use Shopware\Core\Content\Rule\RuleEntity;
use Shopware\Core\Content\Workflow\WorkflowEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class WorkflowRuleEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var \DateTimeInterface
     */
    protected $createdAt;

    /**
     * @var \DateTimeInterface|null
     */
    protected $updatedAt;

    /**
     * @var string
     */
    protected $workflowId;

    /**
     * @var string
     */
    protected $ruleId;

    /**
     * @var WorkflowEntity
     */
    protected $workflow;

    /**
     * @var RuleEntity
     */
    protected $rule;

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getWorkflowId(): string
    {
        return $this->workflowId;
    }

    public function setWorkflowId(string $workflowId): void
    {
        $this->workflowId = $workflowId;
    }

    public function getRuleId(): string
    {
        return $this->ruleId;
    }

    public function setRuleId(string $ruleId): void
    {
        $this->ruleId = $ruleId;
    }

    public function getWorkflow(): WorkflowEntity
    {
        return $this->workflow;
    }

    public function setWorkflow(WorkflowEntity $workflow): void
    {
        $this->workflow = $workflow;
    }

    public function getRule(): RuleEntity
    {
        return $this->rule;
    }

    public function setRule(RuleEntity $rule): void
    {
        $this->rule = $rule;
    }
}
