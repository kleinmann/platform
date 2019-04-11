<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Workflow;

use Shopware\Core\System\SalesChannel\SalesChannelContext;

interface ActionInterface
{
    public function setConfiguration(array $configuration);

    public function execute(SalesChannelContext $context);
}
