<?php
declare(strict_types=1);

namespace App\Services\Deployment;

use App\Services\Deployment\Interfaces\CommandInterface;
use App\Services\Deployment\Traits\ContextAwareTrait;

class Command implements CommandInterface
{
    use ContextAwareTrait;

    /**
     * @var string
     */
    private $command;

    /**
     * @var bool
     */
    private $error = false;

    /**
     * @var array
     */
    private $output = [];

    /**
     * @var string
     */
    private $uniqueId;

    /**
     * Command constructor.
     *
     * @param string $command
     */
    public function __construct(string $command)
    {
        $this->command = $command;
        $this->uniqueId = \uniqid('command', false);
    }

    /**
     * Execute command.
     *
     * @return \App\Services\Deployment\Interfaces\CommandInterface
     */
    public function exec(): CommandInterface
    {
        \exec(\sprintf('%s 2>&1', $this->command), $this->output, $returnCode); // Execute the command

        $this->error = $returnCode !== 0;

        if ($this->error) {
            $this->addError(\sprintf('Error during command: %s', $this->command));
        }

        return $this;
    }

    /**
     * Get command as a string.
     *
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * Get output.
     *
     * @return array
     */
    public function getOutput(): array
    {
        return $this->output;
    }

    /**
     * Get command unique id.
     *
     * @return string
     */
    public function getUniqueId(): string
    {
        return $this->uniqueId;
    }

    /**
     * Determine if command had error.
     *
     * @return bool
     */
    public function isError(): bool
    {
        return $this->error;
    }
}
