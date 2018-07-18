<?php
declare(strict_types=1);

namespace App\Services\Deployment;

use App\Services\Deployment\Interfaces\CommandInterface;
use App\Services\Deployment\Traits\ContextAwareTrait;

class Command extends TerminalItem implements CommandInterface
{
    use ContextAwareTrait;

    /**
     * Execute command.
     *
     * @return \App\Services\Deployment\Interfaces\CommandInterface
     */
    public function exec(): CommandInterface
    {
        $this->startCounting();

        \exec(\sprintf('%s 2>&1', $this->input), $this->output, $returnCode); // Execute the command

        $this->error = $returnCode !== 0;

        if ($this->error) {
            $this->addError(\sprintf('Error during command: %s', $this->input));
        }

        $this->stopCounting();

        return $this;
    }
}
