<?php declare(strict_types=1);

namespace Shopware\Core\Content\Workflow;

use Shopware\Core\Content\Workflow\Aggregate\WorkflowRule\WorkflowRuleCollection;
use Shopware\Core\Content\Workflow\Aggregate\WorkflowWorkflowAction\WorkflowWorkflowActionCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class WorkflowEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $trigger;

    /**
     * @var \DateTimeInterface
     */
    protected $createdAt;

    /**
     * @var \DateTimeInterface|null
     */
    protected $updatedAt;

    /**
     * @var array|null
     */
    protected $attributes;

    /**
     * @var WorkflowRuleCollection|null
     */
    protected $workflowRules;

    /**
     * @var WorkflowWorkflowActionCollection|null
     */
    protected $workflowWorkflowActions;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getTrigger(): string
    {
        return $this->trigger;
    }

    public function setTrigger(string $trigger): void
    {
        $this->trigger = $trigger;
    }

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

    public function getAttributes(): ?array
    {
        return $this->attributes;
    }

    public function setAttributes(?array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function getWorkflowRules(): ?WorkflowRuleCollection
    {
        return $this->workflowRules;
    }

    public function setWorkflowRules(?WorkflowRuleCollection $workflowRules): void
    {
        $this->workflowRules = $workflowRules;
    }

    public function getWorkflowWorkflowActions(): ?WorkflowWorkflowActionCollection
    {
        return $this->workflowWorkflowActions;
    }

    public function setWorkflowWorkflowActions(?WorkflowWorkflowActionCollection $workflowWorkflowActions): void
    {
        $this->workflowWorkflowActions = $workflowWorkflowActions;
    }
}
