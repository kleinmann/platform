<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Workflow\Message;

use Shopware\Core\Framework\MessageQueue\Handler\AbstractMessageHandler;
use Shopware\Core\Framework\Workflow\WorkflowService;

class ActionHandler extends AbstractMessageHandler
{
    /**
     * @var WorkflowService
     */
    private $service;

    public function __construct(WorkflowService $service)
    {
        $this->service = $service;
    }

    /**
     * @param ActionMessage $message
     */
    public function handle($message): void
    {
        $this->service->executeForTrigger($message->getTrigger(), $message->getContext(), $message->getData());
    }

    public static function getHandledMessages(): iterable
    {
        return [
            ActionMessage::class,
        ];
    }
}
