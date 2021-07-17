<?php

declare(strict_types=1);

namespace App\Core;

use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    /**
     * @param iterable $commands
     */
    public function __construct(iterable $commands = [])
    {
        foreach ($commands as $command) {
            $this->add($command);
        }

        parent::__construct();
    }
}
