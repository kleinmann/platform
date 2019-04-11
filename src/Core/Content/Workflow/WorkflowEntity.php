<?php declare(strict_types=1);

namespace Shopware\Core\Content\Workflow;

use Shopware\Core\Content\Workflow\Aggregate\WorkflowAction\WorkflowActionCollection;
use Shopware\Core\Content\Workflow\Aggregate\WorkflowRule\WorkflowRuleCollection;
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
     * @var string
     */
    protected $description;

    /**
     * @var int
     */
    protected $priority;

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
     * @var WorkflowActionCollection|null
     */
    protected $workflowActions;

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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
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

    public function getWorkflowActions(): ?WorkflowActionCollection
    {
        return $this->workflowActions;
    }

    public function setWorkflowActions(?WorkflowActionCollection $workflowActions): void
    {
        $this->workflowActions = $workflowActions;
    }
}
