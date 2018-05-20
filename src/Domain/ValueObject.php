<?php

declare(strict_types=1);

namespace Domain;

interface ValueObject
{
    public function equals(ValueObject $other): bool;
}
