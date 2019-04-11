<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Workflow\Action;

use Shopware\Core\System\SalesChannel\SalesChannelContext;

interface ActionInterface
{
    public function getHandlerIdentifier(): string;

    public function execute(SalesChannelContext $context, array $configuration): void;
}
