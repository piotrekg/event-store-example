<?php

declare(strict_types=1);

namespace Domain\Basket;

interface BasketRepository
{
    public function get(BasketId $basketId): ?Basket;

    public function save(Basket $basket): void;
}
