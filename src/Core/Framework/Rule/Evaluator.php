<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Rule;

use Shopware\Core\Content\Rule\RuleCollection;
use Shopware\Core\Content\Rule\RuleEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class Evaluator
{
    /**
     * @var RuleCollection
     */
    private $rules;

    /**
     * @var EntityRepositoryInterface
     */
    private $repository;

    public function __construct(EntityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getMatchingRules(RuleScope $ruleScope, Context $context): RuleCollection
    {
        $rules = $this->loadRules($context);

        $rules->sortByPriority();

        return $rules->filterMatchingRules($ruleScope);
    }

    private function loadRules(Context $context): RuleCollection
    {
        if ($this->rules !== null) {
            return $this->rules;
        }

        /** @var RuleCollection $rules */
        $rules = $this->repository->search(new Criteria(), $context)->getEntities();

        /** @var RuleEntity $rule */
        foreach ($rules as $key => $rule) {
            if ($rule->isInvalid() || !$rule->getPayload()) {
                $rules->remove($key);
            }
        }

        return $this->rules = $rules;
    }
}
