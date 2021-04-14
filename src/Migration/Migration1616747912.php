<?php declare(strict_types=1);

namespace ProductPromotionExam\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1616747912 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1616747912;
    }

    public function update(Connection $connection): void
    {
        $connection->exec("
            CREATE TABLE IF NOT EXISTS `promotion_exam`  (
                    `id` BINARY(16) NOT NULL,
                    `name` VARCHAR(255) NOT NULL,
                    `discount_rate` DOUBLE NOT NULL,
                    `start_date` DATETIME(3) NULL,
                    `expired_date` DATETIME(3) NULL,
                    `product_version_id` BINARY(16) NULL,
                    `product_id` BINARY(16) NULL,
                    `created_at` DATETIME(3) NOT NULL,
                    `updated_at` DATETIME(3) NULL,
                    PRIMARY KEY (`id`),
                    KEY `fk.promotion_exam.product_id` (`product_id`,`product_version_id`),
                    CONSTRAINT `fk.promotion_exam.product_id` FOREIGN KEY (`product_id`,`product_version_id`) REFERENCES `product` (`id`,`version_id`) ON DELETE SET NULL ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
        );
    }

    public function updateDestructive(Connection $connection): void
    {
        $connection->exec("DROP TABLE `promotion_exam`;");
    }
}
