<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Workflow;

use Shopware\Core\Checkout\CheckoutRuleScope;
use Shopware\Core\Content\Workflow\Aggregate\WorkflowRule\WorkflowRuleEntity;
use Shopware\Core\Content\Workflow\WorkflowCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Rule\Evaluator;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class WorkflowService
{
    /**
     * @var EntityRepositoryInterface
     */
    private $workflowRepository;

    /**
     * @var Evaluator
     */
    private $evaluator;

    /**
     * @var ActionProvider
     */
    private $actionProvider;

    public function __construct(EntityRepositoryInterface $workflowRepository, Evaluator $evaluator, ActionProvider $actionProvider)
    {
        $this->workflowRepository = $workflowRepository;
        $this->evaluator = $evaluator;
        $this->actionProvider = $actionProvider;
    }

    public function executeForTrigger(string $trigger, SalesChannelContext $context): void
    {
        /** @var WorkflowCollection $workflows */
        $workflows = $this->workflowRepository->search(
            (new Criteria())->addFilter(new EqualsFilter('trigger', $trigger))
                ->addAssociation('workflowRules')
                ->addAssociation('workflowActions'), $context->getContext()
        )->getEntities();

        $matchingRules = $this->evaluator->getMatchingRules(new CheckoutRuleScope($context), $context->getContext());

        foreach ($workflows->getIterator() as $workflow) {
            $workflowRules = array_map(
                function (WorkflowRuleEntity $workflowRule) {
                    return $workflowRule->getRule();
                },
                $workflow->getWorkflowRules()->getElements()
            );

            if (empty(array_intersect($workflowRules, $matchingRules->getElements()))) {
                continue;
            }

            foreach ($workflow->getWorkflowWorkflowActions() as $workflowWorkflowAction) {
                $action = $this->actionProvider->getActionForHandlerIdentifier($workflowWorkflowAction->getWorkflowAction()->getHandlerIdentifier());
                $action->execute($context, $workflowWorkflowAction->getConfiguration());
            }
        }
    }
}
