<?php

declare(strict_types=1);

namespace Infrastructure\Basket\Projection;

use Doctrine\DBAL\Driver\Connection;
use Domain\Basket\BasketId;
use Domain\Product\ProductId;
use Infrastructure\Basket\DTO\BasketDTO;
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

    public function findById(BasketId $basketId): ?BasketDTO
    {
        $stmt = $this->connection->prepare(sprintf('
            SELECT * FROM %s as b
            LEFT JOIN %s as bp ON b.id = bp.basket_id 
            WHERE b.id = :basket_id
        ', Table::BASKET, Table::BASKET_PRODUCT));
        $stmt->bindValue('basket_id', $basketId->toString());
        $stmt->execute();

        $result = $stmt->fetchAll(\PDO::FETCH_OBJ);

        if (false === $result) {
            return null;
        }

        $products = [];
        foreach ($result as $item) {
            $products[] = ProductId::fromString($item->product_id);
        }

        $result = new BasketDTO(
            BasketId::fromString($result[0]->id),
            $products
        );

        return $result;
    }
}
