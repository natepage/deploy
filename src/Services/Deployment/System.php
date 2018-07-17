<?php
declare(strict_types=1);

namespace App\Services\Deployment;

use App\Services\Deployment\Interfaces\SystemInterface;
use App\Services\Deployment\Traits\ContextAwareTrait;
use App\Services\Deployment\Traits\ShellAwareTrait;

class System implements SystemInterface
{
    use ContextAwareTrait;
    use ShellAwareTrait;

    /**
     * @var array
     */
    private $binaries;

    /**
     * Check if backup dir exists and is writable.
     *
     * @return bool
     */
    public function checkBackupDir(): bool
    {
        $backupDir = $this->getConfiguration()->getBackupDir();

        if ($backupDir === null) {
            return true;
        }

        if (\is_dir($backupDir) === false || \is_writable($backupDir)) {
            $this->addError(\sprintf(
                'BACKUP_DIR `%s` does not exists or is not writable.',
                $backupDir
            ));

            return false;
        }

        return true;
    }

    /**
     * Return list of binaries as [$binary => $version].
     *
     * @return array
     */
    public function getBinaries(): array
    {
        if ($this->binaries !== null) {
            return $this->binaries;
        }

        $binaries = [];
        $required = $this->getConfiguration()->getRequiredBinaries();

        // Require tar if backup dir configured
        if ($this->getConfiguration()->getBackupDir() !== null) {
            $required[] = 'tar';
        }
        // Required composer if composer is configured
        if ($this->getConfiguration()->useComposer()) {
            $required[] = 'composer --no-ansi';
        }

        foreach ($required as $binary) {
            $path = $this->shellExec(\sprintf('command -v %s', $binary));

            if ($path === '') {
                $this->addError(\sprintf(
                    '%s not available. It needs to be installed on the server for this script to work.',
                    $binary
                ));

                continue;
            }

            $binaries[$path] = $this->getBinaryVersion($binary);
        }

        return $this->binaries = $binaries;
    }

    /**
     * Return current username.
     *
     * @return string
     */
    public function getCurrentUser(): string
    {
        return $this->shellExec('whoami');
    }

    /**
     * Set env values.
     *
     * @return void
     */
    public function setEnvValues(): void
    {
        foreach ($this->getConfiguration()->getEnv() as $env => $value) {
            \putenv(\sprintf('%s=%s', $env, $value ?? ''));
        }
    }

    /**
     * Get binary version.
     *
     * @param string $binary
     *
     * @return string
     */
    private function getBinaryVersion(string $binary): string
    {
        $version = \explode("\n", $this->shellExec(\sprintf('%s --version', $binary)));

        return $version[0] ?? 'Version Not Found';
    }
}
