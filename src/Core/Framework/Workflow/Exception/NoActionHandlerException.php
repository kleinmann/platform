<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Workflow\Exception;

use Shopware\Core\Framework\ShopwareHttpException;

class NoActionHandlerException extends ShopwareHttpException
{
    public function __construct(string $handlerIdentifier)
    {
        parent::__construct(
            'No action handler for handler identifier {{ handlerIdentifier }} found. There must be an action handler for each handler identifier.',
            ['handlerIdentifier' => $handlerIdentifier]
        );
    }

    public function getErrorCode(): string
    {
        return 'WORKFLOW__NO_ACTION_HANDLER';
    }
}
