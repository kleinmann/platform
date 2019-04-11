<?php declare(strict_types=1);

namespace Shopware\Core\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1554905417CreateWorkflow extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1554905417;
    }

    public function update(Connection $connection): void
    {
        $connection->exec(
            'CREATE TABLE `workflow`
            (
                `id`          BINARY(16)   NOT NULL
                    PRIMARY KEY,
                `name`        VARCHAR(255) NOT NULL,
                `trigger`     VARCHAR(255) NOT NULL,
                `description` LONGTEXT     NULL,
                `priority`    INT(11)      NOT NULL,
                `attributes`  JSON         NULL,
                `created_at`  DATETIME     NOT NULL,
                `updated_at`  DATETIME     NULL
            );

            CREATE INDEX `idx.trigger`
                ON `workflow` (`trigger`);
            CREATE INDEX `idx.priority`
                ON `workflow` (`priority`);'
        );

        $connection->exec(
            'CREATE TABLE `workflow_action`
            (
                `id`                 BINARY(16)   NOT NULL
                    PRIMARY KEY,
                `workflow_id`        BINARY(16)   NOT NULL,
                `handler_identifier` VARCHAR(255) NOT NULL,
                `attributes`         JSON         NULL,
                `configuration`      JSON         NOT NULL,
                `priority`           INT(11)      NOT NULL,
                `created_at`         DATETIME     NOT NULL,
                `updated_at`         DATETIME     NULL,
                CONSTRAINT `fk.workflow_action.workflow_id`
                    FOREIGN KEY (`workflow_id`) REFERENCES `workflow` (`id`)
                        ON DELETE CASCADE
            );

            CREATE INDEX `idx.handler_identifier`
                ON `workflow_action` (`handler_identifier`);
            CREATE INDEX `idx.priority`
                ON `workflow_action` (`priority`);'
        );

        $connection->exec(
            'CREATE TABLE `workflow_rule`
            (
                `id`          BINARY(16) NOT NULL
                    PRIMARY KEY,
                `workflow_id` BINARY(16) NOT NULL,
                `rule_id`     BINARY(16) NOT NULL,
                `created_at`  DATETIME   NOT NULL,
                `updated_at`  DATETIME   NULL,
                CONSTRAINT `fk.workflow_rule.rule_id`
                    FOREIGN KEY (`rule_id`) REFERENCES `rule` (`id`)
                        ON DELETE CASCADE,
                CONSTRAINT `fk.workflow_rule.workflow_id`
                    FOREIGN KEY (`workflow_id`) REFERENCES `workflow` (`id`)
                        ON DELETE CASCADE
            );'
        );
    }

    public function updateDestructive(Connection $connection): void
    {
        // nth
    }
}
