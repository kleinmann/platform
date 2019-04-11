<?php declare(strict_types=1);

namespace Shopware\Core\Content\Workflow\Aggregate\WorkflowAction;

use Shopware\Core\Content\Workflow\Aggregate\WorkflowWorkflowAction\WorkflowWorkflowActionCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class WorkflowActionEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $handlerIdentifier;

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
     * @var array|null
     */
    protected $fields;

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

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
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

    public function getHandlerIdentifier(): string
    {
        return $this->handlerIdentifier;
    }

    public function setHandlerIdentifier(string $handlerIdentifier): void
    {
        $this->handlerIdentifier = $handlerIdentifier;
    }

    public function getFields(): ?array
    {
        return $this->fields;
    }

    public function setFields(?array $fields): void
    {
        $this->fields = $fields;
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
