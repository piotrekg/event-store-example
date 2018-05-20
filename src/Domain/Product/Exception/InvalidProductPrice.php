<?php

declare(strict_types=1);

namespace Domain\Product\Exception;

final class InvalidProductPrice extends \InvalidArgumentException
{
    public static function reason(string $message): self
    {
        return new self('Invalid product price because '.$message);
    }
}
