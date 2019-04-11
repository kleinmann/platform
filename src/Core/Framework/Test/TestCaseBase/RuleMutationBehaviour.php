<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Test\TestCaseBase;

use Shopware\Core\Framework\Rule\Evaluator;
use Shopware\Core\Framework\Test\TestCaseHelper\ReflectionHelper;

trait RuleMutationBehaviour
{
    /**
     * @before
     */
    public function resetRuleCache(): void
    {
        $evaluator = $this->getContainer()->get(Evaluator::class);
        $rulesProperty = ReflectionHelper::getProperty(Evaluator::class, 'rules');
        $rulesProperty->setValue($evaluator, null);
    }
}
