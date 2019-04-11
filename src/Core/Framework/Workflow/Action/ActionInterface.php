<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Workflow\Action;

use Shopware\Core\System\SalesChannel\SalesChannelContext;

interface ActionInterface
{
    public const FIELD_TYPE_TEXT = 'text';
    public const FIELD_TYPE_TEXTAREA = 'textarea';
    public const FIELD_TYPES = [
        self::FIELD_TYPE_TEXT,
        self::FIELD_TYPE_TEXTAREA,
    ];

    public function getHandlerIdentifier(): string;

    public function execute(SalesChannelContext $context, array $configuration): void;

    public function getFields(): array;
}
