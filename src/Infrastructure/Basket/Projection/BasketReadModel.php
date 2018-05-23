<?php

declare(strict_types=1);

namespace Infrastructure\Basket\Projection;

use Doctrine\DBAL\Driver\Connection;
use Infrastructure\Table;
use Prooph\EventStore\Projection\AbstractReadModel;

final class BasketReadModel extends AbstractReadModel
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function init(): void
    {
        $basketTableName = Table::BASKET;
        $basketProductsTableName = Table::BASKET_PRODUCT;

        $sql = <<<EOT
CREATE TABLE `$basketTableName` (
  `id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `products` JSON CHECK (JSON_VALID(products)),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `$basketProductsTableName` (
  `basket_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `product_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`basket_id`, `product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

EOT;

        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }

    public function isInitialized(): bool
    {
        $tableName = Table::BASKET;

        $sql = "SHOW TABLES LIKE '$tableName';";

        $statement = $this->connection->prepare($sql);
        $statement->execute();

        $result = $statement->fetch();

        if (false === $result) {
            return false;
        }

        return true;
    }

    public function reset(): void
    {
        $tableName = Table::BASKET;

        $sql = "TRUNCATE TABLE $tableName;";

        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }

    public function delete(): void
    {
        $tableName = Table::BASKET;

        $sql = "DROP TABLE $tableName;";

        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }

    protected function insert(array $data): void
    {
        $this->connection->insert(Table::BASKET, $data);
    }

    protected function addProduct(string $basketId, string $productId): void
    {
        $stmt = $this->connection->prepare(sprintf('INSERT INTO %s SET basket_id = :basket_id, product_id = :product_id', Table::BASKET_PRODUCT));

        $stmt->bindValue('basket_id', $basketId);
        $stmt->bindValue('product_id', $productId);

        $stmt->execute();
    }
}
