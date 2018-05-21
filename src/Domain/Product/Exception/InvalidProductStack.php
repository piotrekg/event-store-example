<?php

declare(strict_types=1);

namespace Domain\Product\Exception;

final class InvalidProductStack extends \InvalidArgumentException
{
    public static function reason(string $message): self
    {
        return new self('Invalid product stack because '.$message);
    }
}
