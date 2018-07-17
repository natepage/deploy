<?php
declare(strict_types=1);

namespace App\Services\Deployment\Interfaces;

interface ConfigurationInterface
{
    /**
     * Get all configuration as an associative array.
     *
     * @return array
     */
    public function getAll(): array;

    /**
     * Return optional backup dir.
     *
     * @return null|string
     */
    public function getBackupDir(): ?string;

    /**
     * Return branch to clone.
     *
     * @return string
     */
    public function getBranch(): string;

    /**
     * Return composer home.
     *
     * @return null|string
     */
    public function getComposerHome(): ?string;

    /**
     * Return composer options.
     *
     * @return string
     */
    public function getComposerOptions(): string;

    /**
     * Get env values.
     *
     * @return array
     */
    public function getEnv(): array;

    /**
     * Get excludes for rsync.
     *
     * @return array
     */
    public function getExcludes(): array;

    /**
     * Return remote repository to clone.
     *
     * @return string
     */
    public function getRepository(): string;

    /**
     * Return list of required binaries.
     *
     * @return array
     */
    public function getRequiredBinaries(): array;

    /**
     * Return target dir to deploy.
     *
     * @return string
     */
    public function getTargetDir(): string;

    /**
     * Return time limit for each command.
     *
     * @return int
     */
    public function getTimeLimit(): int;

    /**
     * Return tmp dir.
     *
     * @return string
     */
    public function getTmpDir(): string;

    /**
     * Return version file.
     *
     * @return string
     */
    public function getVersionFile(): string;

    /**
     * Determine if clean up after proceed.
     *
     * @return bool
     */
    public function isCleanUp(): bool;

    /**
     * Determine if delete files after proceed.
     *
     * @return bool
     */
    public function isDeleteFiles(): bool;

    /**
     * Determine if deployment uses composer.
     *
     * @return bool
     */
    public function useComposer(): bool;
}
