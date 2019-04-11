<?php declare(strict_types=1);

namespace Shopware\Core\Content\Workflow\Aggregate\WorkflowAction;

use Shopware\Core\Content\Workflow\WorkflowEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class WorkflowActionEntity extends Entity
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
     * @var array
     */
    protected $configuration;

    /**
     * @var string
     */
    protected $handlerIdentifier;

    /**
     * @var string
     */
    protected $workflowId;

    /**
     * @var string
     */
    protected $workflowActionId;

    /**
     * @var WorkflowEntity
     */
    protected $workflow;

    /**
     * @var WorkflowActionEntity
     */
    protected $workflowAction;

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

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }

    public function getHandlerIdentifier(): string
    {
        return $this->handlerIdentifier;
    }

    public function setHandlerIdentifier(string $handlerIdentifier): void
    {
        $this->handlerIdentifier = $handlerIdentifier;
    }

    public function getWorkflowId(): string
    {
        return $this->workflowId;
    }

    public function setWorkflowId(string $workflowId): void
    {
        $this->workflowId = $workflowId;
    }

    public function getWorkflowActionId(): string
    {
        return $this->workflowActionId;
    }

    public function setWorkflowActionId(string $workflowActionId): void
    {
        $this->workflowActionId = $workflowActionId;
    }

    public function getWorkflow(): WorkflowEntity
    {
        return $this->workflow;
    }

    public function setWorkflow(WorkflowEntity $workflow): void
    {
        $this->workflow = $workflow;
    }

    public function getWorkflowAction(): WorkflowActionEntity
    {
        return $this->workflowAction;
    }

    public function setWorkflowAction(WorkflowActionEntity $workflowAction): void
    {
        $this->workflowAction = $workflowAction;
    }
}
