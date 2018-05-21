<?php

declare(strict_types=1);

namespace Domain\Product\Exception;

final class InvalidProductStock extends \InvalidArgumentException
{
    public static function reason(string $message): self
    {
        return new self('Invalid product stock because '.$message);
    }
}
