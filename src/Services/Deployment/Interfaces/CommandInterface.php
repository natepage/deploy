<?php
declare(strict_types=1);

namespace App\Services\Deployment\Interfaces;

interface CommandInterface extends TerminalItemInterface
{
    /**
     * Execute command.
     *
     * @return \App\Services\Deployment\Interfaces\CommandInterface
     */
    public function exec(): self;
}
