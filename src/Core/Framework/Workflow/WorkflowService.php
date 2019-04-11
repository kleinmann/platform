<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Workflow;

use Psr\Log\LoggerInterface;
use Shopware\Core\Content\Workflow\Aggregate\WorkflowRule\WorkflowRuleEntity;
use Shopware\Core\Content\Workflow\WorkflowCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Struct\Collection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class WorkflowService
{
    /**
     * @var EntityRepositoryInterface
     */
    private $workflowRepository;

    /**
     * @var ActionProvider
     */
    private $actionProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(EntityRepositoryInterface $workflowRepository, ActionProvider $actionProvider, LoggerInterface $logger)
    {
        $this->workflowRepository = $workflowRepository;
        $this->actionProvider = $actionProvider;
        $this->logger = $logger;
    }

    public function executeForTrigger(string $trigger, SalesChannelContext $context, Collection $data): void
    {
        /** @var WorkflowCollection $workflows */
        $workflows = $this->workflowRepository->search(
            (new Criteria())->addFilter(new EqualsFilter('trigger', $trigger))
                ->addAssociation('workflowRules')
                ->addAssociation('workflowActions')
                ->addSorting(new FieldSorting('priority', FieldSorting::DESCENDING)),
            $context->getContext()
        )->getEntities();

        foreach ($workflows->getIterator() as $workflow) {
            $workflowRuleIds = array_map(
                function (WorkflowRuleEntity $workflowRule) {
                    return $workflowRule->getRule()->getId();
                },
                $workflow->getWorkflowRules()->getElements()
            );

            if (!empty($workflowRuleIds) && empty(array_intersect($workflowRuleIds, $context->getRuleIds()))) {
                continue;
            }

            $workflowActions = $workflow->getWorkflowActions();
            $workflowActions->sortByPriority();

            foreach ($workflowActions as $workflowAction) {
                $action = $this->actionProvider->getActionForHandlerIdentifier($workflowAction->getHandlerIdentifier());

                try {
                    $action->execute($workflowAction->getConfiguration(), $data);
                } catch (\Throwable $e) {
                    $this->logger->error(sprintf('Error on workflow action execution for workflow action %s', $workflowAction->getId()), ['exception' => $e]);
                }
            }
        }
    }
}
