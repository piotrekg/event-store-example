<?php

declare(strict_types=1);

namespace Infrastructure\Product\Projection;

use Doctrine\DBAL\Driver\Connection;
use Domain\Product\ProductId;
use Infrastructure\Table;

class ProductFinder
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_OBJ);
    }

    public function findAll(): array
    {
        return $this->connection->fetchAll(sprintf('SELECT * FROM %s', Table::PRODUCT));
    }

    public function findById(ProductId $productId): ?\stdClass
    {
        $stmt = $this->connection->prepare(sprintf('SELECT * FROM %s WHERE id = :product_id', Table::PRODUCT));
        $stmt->bindValue('product_id', $productId->toString());
        $stmt->execute();

        $result = $stmt->fetch();

        if (false === $result) {
            return null;
        }

        return $result;
    }
}
