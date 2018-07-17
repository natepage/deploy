<?php
declare(strict_types=1);

namespace App\Services\Deployment\Interfaces;

interface SystemInterface
{
    /**
     * Check if backup dir exists and is writable.
     *
     * @return bool
     */
    public function checkBackupDir(): bool;

    /**
     * Return current username.
     *
     * @return string
     */
    public function getCurrentUser(): string;

    /**
     * Return list of binaries as [$binary => $version].
     *
     * @return array
     */
    public function getBinaries(): array;

    /**
     * Set env values.
     *
     * @return void
     */
    public function setEnvValues(): void;
}
