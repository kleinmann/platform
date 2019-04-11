<?php declare(strict_types=1);

namespace Shopware\Core\Content\Rule;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\Rule\RuleScope;

/**
 * @method void            add(RuleEntity $entity)
 * @method void            set(string $key, RuleEntity $entity)
 * @method RuleEntity[]    getIterator()
 * @method RuleEntity[]    getElements()
 * @method RuleEntity|null get(string $key)
 * @method RuleEntity|null first()
 * @method RuleEntity|null last()
 */
class RuleCollection extends EntityCollection
{
    public function filterMatchingRules(RuleScope $scope)
    {
        return $this->filter(
            function (RuleEntity $rule) use ($scope) {
                return $rule->getPayload()->match($scope)->matches();
            }
        );
    }

    public function sortByPriority(): void
    {
        $this->sort(function (RuleEntity $a, RuleEntity $b) {
            return $b->getPriority() <=> $a->getPriority();
        });
    }

    protected function getExpectedClass(): string
    {
        return RuleEntity::class;
    }
}
