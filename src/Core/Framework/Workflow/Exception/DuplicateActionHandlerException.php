<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Workflow\Exception;

use Shopware\Core\Framework\ShopwareHttpException;

class DuplicateActionHandlerException extends ShopwareHttpException
{
    public function __construct(string $handlerIdentifier)
    {
        parent::__construct(
            'Duplicate action handler for handler identifier {{ handlerIdentifier }} found. There must only be one action handler per handler identifier.',
            ['handlerIdentifier' => $handlerIdentifier]
        );
    }

    public function getErrorCode(): string
    {
        return 'WORKFLOW__DUPLICATE_ACTION_HANDLER';
    }
}
