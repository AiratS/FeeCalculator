<?php

declare(strict_types=1);

namespace App\Service;

class FileFormatResolver
{
    /**
     * @param string $filePath
     * @return string
     */
    public function resolveFormat(string $filePath): string
    {
        return pathinfo($filePath, PATHINFO_EXTENSION);
    }
}
