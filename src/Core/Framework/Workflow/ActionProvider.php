<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Workflow;

use Shopware\Core\Framework\Workflow\Action\ActionInterface;
use Shopware\Core\Framework\Workflow\Exception\DuplicateActionHandlerException;
use Shopware\Core\Framework\Workflow\Exception\NoActionHandlerException;

class ActionProvider
{
    /**
     * @var iterable|ActionInterface[]
     */
    private $actions;

    /**
     * @param iterable|ActionInterface[] $actions
     *
     * @throws DuplicateActionHandlerException
     */
    public function __construct(iterable $actions)
    {
        $this->ensureUniqueHandlers($actions);

        foreach ($actions as $action) {
            $this->actions[$action->getHandlerIdentifier()] = $action;
        }
    }

    /**
     * @throws NoActionHandlerException
     */
    public function getActionForHandlerIdentifier(string $handlerIdentifier): ActionInterface
    {
        if (!isset($this->actions[$handlerIdentifier])) {
            throw new NoActionHandlerException($handlerIdentifier);
        }

        return $this->actions[$handlerIdentifier];
    }

    /**
     * @param iterable|ActionInterface[] $actions
     *
     * @throws DuplicateActionHandlerException
     */
    private function ensureUniqueHandlers(iterable $actions): void
    {
        $seenHandlerIdentifiers = [];

        foreach ($actions as $action) {
            if (isset($seenHandlerIdentifiers[$action->getHandlerIdentifier()])) {
                throw new DuplicateActionHandlerException($action->getHandlerIdentifier());
            }

            $seenHandlerIdentifiers[$action->getHandlerIdentifier()] = $action->getHandlerIdentifier();
        }
    }
}
