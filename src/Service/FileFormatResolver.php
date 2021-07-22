<?php

declare(strict_types=1);

namespace App\Service;

class FileFormatResolver
{
    public function resolveFormat(string $filePath): string
    {
        return pathinfo($filePath, PATHINFO_EXTENSION);
    }
}
