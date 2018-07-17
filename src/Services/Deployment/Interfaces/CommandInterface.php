<?php
declare(strict_types=1);

namespace App\Services\Deployment\Interfaces;

interface CommandInterface
{
    /**
     * Execute command.
     *
     * @return \App\Services\Deployment\Interfaces\CommandInterface
     */
    public function exec(): self;

    /**
     * Get command as a string.
     *
     * @return string
     */
    public function getCommand(): string;

    /**
     * Get output.
     *
     * @return array
     */
    public function getOutput(): array;

    /**
     * Get command unique id.
     *
     * @return string
     */
    public function getUniqueId(): string;

    /**
     * Determine if command had error.
     *
     * @return bool
     */
    public function isError(): bool;
}
