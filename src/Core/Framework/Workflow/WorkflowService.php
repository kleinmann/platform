<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Workflow;

use Shopware\Core\Checkout\CheckoutRuleScope;
use Shopware\Core\Content\Workflow\Aggregate\WorkflowRule\WorkflowRuleEntity;
use Shopware\Core\Content\Workflow\WorkflowCollection;
use Shopware\Core\Framework\Context;
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
     * @var Context
     */
    private $context;

    /**
     * @var Evaluator
     */
    private $evaluator;

    public function executeForTrigger(string $trigger, SalesChannelContext $context)
    {
        /** @var WorkflowCollection $workflows */
        $workflows = $this->workflowRepository->search((new Criteria())->addFilter(new EqualsFilter('trigger', $trigger))->addAssociation('workflowRules'), $this->context)->getEntities();

        $matchingRules = $this->evaluator->getMatchingRules(new CheckoutRuleScope($context), $context->getContext());

        foreach ($workflows->getIterator() as $workflow) {
            // TODO: If workflow's rules match, execute actions
            $workflowRules = array_map(
                function (WorkflowRuleEntity $workflowRule) {
                    return $workflowRule->getRule();
                },
                $workflow->getWorkflowRules()->getElements()
            );

            if (count(array_diff($workflowRules, $matchingRules->getElements())) === 0) {
                // TODO: Fetch matching action for handler_identifier of workflow's actions
                // TODO: Execute actions
            }
        }
    }
}
