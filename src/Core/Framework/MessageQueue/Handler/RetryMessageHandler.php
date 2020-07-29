<?php declare(strict_types=1);

namespace Shopware\Core\Framework\MessageQueue\Handler;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\MessageQueue\DeadMessage\DeadMessageEntity;
use Shopware\Core\Framework\MessageQueue\Message\RetryMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class RetryMessageHandler extends AbstractMessageHandler
{
    /**
     * @var EntityRepositoryInterface
     */
    private $deadMessageRepository;

    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    public function __construct(
        EntityRepositoryInterface $deadMessageRepository,
        MessageBusInterface $messageBus
    ) {
        $this->deadMessageRepository = $deadMessageRepository;
        $this->messageBus = $messageBus;
    }

    /**
     * @param RetryMessage $message
     */
    public function handle($message): void
    {
        /** @var DeadMessageEntity|null $deadMessage */
        $deadMessage = $this->deadMessageRepository
            ->search(new Criteria([$message->getDeadMessageId()]), Context::createDefaultContext())
            ->get($message->getDeadMessageId());

        if (!$deadMessage) {
            return;
        }

        $this->messageBus->dispatch($deadMessage->getOriginalMessage());

        $this->deadMessageRepository->delete([
            [
                'id' => $deadMessage->getId(),
            ],
        ], Context::createDefaultContext());
    }

    public static function getHandledMessages(): iterable
    {
        return [RetryMessage::class];
    }
}
