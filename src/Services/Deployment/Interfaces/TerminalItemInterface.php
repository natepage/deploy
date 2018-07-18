<?php
declare(strict_types=1);

namespace App\Services\Deployment\Interfaces;

interface TerminalItemInterface
{
    /**
     * Get terminal item input.
     *
     * @return string
     */
    public function getInput(): string;

    /**
     * Get terminal item output.
     *
     * @return array
     */
    public function getOutput(): array;

    /**
     * Get terminal item unique id.
     *
     * @return string
     */
    public function getUniqueId(): string;

    /**
     * Determine if terminal item had error.
     *
     * @return bool
     */
    public function isError(): bool;
}
