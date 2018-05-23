<?php

declare(strict_types=1);

namespace Infrastructure\Basket\Projection;

use Doctrine\DBAL\Driver\Connection;
use Domain\Basket\BasketId;
use Infrastructure\Table;

class BasketFinder
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): array
    {
        return $this->connection->fetchAll(sprintf('SELECT * FROM %s', Table::BASKET));
    }

    public function findById(BasketId $basketId): ?\stdClass
    {
        $stmt = $this->connection->prepare(sprintf('SELECT * FROM %s WHERE id = :product_id', Table::BASKET));
        $stmt->bindValue('basket_id', $basketId->toString());
        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_OBJ);

        if (false === $result) {
            return null;
        }

        return $result;
    }
}
