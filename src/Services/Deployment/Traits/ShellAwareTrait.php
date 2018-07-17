<?php
declare(strict_types=1);

namespace App\Services\Deployment\Traits;

trait ShellAwareTrait
{
    /**
     * Execute SHELL command and return output.
     *
     * @param string $command
     *
     * @return string
     */
    private function shellExec(string $command): string
    {
        return \trim(\shell_exec($command)) ?? '';
    }
}
