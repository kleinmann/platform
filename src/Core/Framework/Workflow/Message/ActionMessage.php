<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Workflow\Message;

use Shopware\Core\Framework\Struct\StructCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class ActionMessage
{
    /**
     * @var string
     */
    private $trigger;

    /**
     * @var StructCollection
     */
    private $data;

    /**
     * @var SalesChannelContext
     */
    private $context;

    public function __construct(string $trigger, StructCollection $data, SalesChannelContext $context)
    {
        $this->trigger = $trigger;
        $this->data = $data;
        $this->context = $context;
    }

    public function getTrigger(): string
    {
        return $this->trigger;
    }

    public function getData(): StructCollection
    {
        return $this->data;
    }

    public function getContext(): SalesChannelContext
    {
        return $this->context;
    }
}
