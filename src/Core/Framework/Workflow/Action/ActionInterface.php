<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Workflow\Action;

use Shopware\Core\Framework\Struct\Collection;

interface ActionInterface
{
    public function getHandlerIdentifier(): string;

    public function execute(array $configuration, Collection $data): void;
}
