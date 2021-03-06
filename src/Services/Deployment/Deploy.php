<?php
declare(strict_types=1);

namespace App\Services\Deployment;

use App\Services\Deployment\Interfaces\ConfigurationInterface;
use App\Services\Deployment\Interfaces\ContextInterface;
use App\Services\Deployment\Interfaces\SystemInterface;
use App\Services\Deployment\Traits\CanCountTime;
use DateTime;

class Deploy implements ContextInterface
{
    use CanCountTime;

    /**
     * @var \App\Services\Deployment\Interfaces\ConfigurationInterface
     */
    private $configuration;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var \App\Services\Deployment\Interfaces\SystemInterface
     */
    private $system;

    /**
     * @var array
     */
    private $terminalItems = [];

    /**
     * Deploy constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->date = new DateTime();
        $this->configuration = new Configuration($config);

        $this->system = new System();
        $this->system->setContext($this);
    }

    /**
     * Add error to deployment context.
     *
     * @param string $description
     *
     * @return \App\Services\Deployment\Interfaces\ContextInterface
     */
    public function addError(string $description): ContextInterface
    {
        $this->errors[] = new Error($description);

        return $this;
    }

    /**
     * Trigger the deployment.
     *
     * @return void
     */
    public function deploy(): void
    {
        $this->startCounting();

        $this->terminalItems[] = $prepare = new TerminalItem('Prepare deployment', [
            'Check required binaries',
            'Set environment values',
            'Initiate deployment commands'
        ]);
        $prepare->startCounting();

        // Check for required binaries
        $this->system->getBinaries();
        // Set ENV values
        $this->system->setEnvValues();
        // Initiate commands
        $this->initCommands();

        $prepare->stopCounting();

        // Check backup dir
        if (empty($this->errors) === false || $this->system->checkBackupDir() === false) {
            return;
        }

        foreach ($this->terminalItems as $item) {
            if (($item instanceof Command) === false) {
                continue;
            }

            $this->resetTimeLimit();
            $this->useTmpDir();

            // If command is successful, continue
            if ($item->exec()->isError() === false) {
                continue;
            }

            if ($this->configuration->isCleanUp()) {
                $this->terminalItems['clean_up']->exec();
            }

            break;
        }

        $this->stopCounting();
    }

    /**
     * Return deployment configuration.
     *
     * @return \App\Services\Deployment\Interfaces\ConfigurationInterface
     */
    public function getConfiguration(): ConfigurationInterface
    {
        return $this->configuration;
    }

    /**
     * Return deployment date.
     *
     * @return \DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * Return list of deployment errors.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Return deployment system.
     *
     * @return \App\Services\Deployment\Interfaces\SystemInterface
     */
    public function getSystem(): SystemInterface
    {
        return $this->system;
    }

    /**
     * Return list of deployment terminal items.
     *
     * @return array
     */
    public function getTerminalItems(): array
    {
        return $this->terminalItems;
    }

    /**
     * Add deployment command.
     *
     * @param string $command
     *
     * @return \App\Services\Deployment\Deploy
     */
    private function addCommand(string $command): self
    {
        $this->terminalItems[] = new Command($command);

        return $this;
    }

    /**
     * Initiate deployment commands.
     *
     * @return void
     */
    private function initCommands(): void
    {
        $this->initTmpDirCommands();

        $this
            ->addCommand('git submodule update --init --recursive')
            ->addCommand(\sprintf(
                'git --git-dir="%s.git" --work-tree="%s" describe --always > %s',
                $this->configuration->getTmpDir(),
                $this->configuration->getTmpDir(),
                $this->configuration->getVersionFile()
            ));

        if ($this->configuration->getVersionFile() !== '') {
            $this->addCommand(\sprintf(
                'git --git-dir="%s.git" --work-tree="%s" describe --always > %s',
                $this->configuration->getTmpDir(),
                $this->configuration->getTmpDir(),
                $this->configuration->getVersionFile()
            ));
        }

        if ($this->configuration->getBackupDir() !== null) {
            $this->addCommand(\sprintf(
                "tar --exclude='%s*' -czf %s/%s-%s-%s.tar.gz %s*",
                $this->configuration->getBackupDir(),
                $this->configuration->getBackupDir(),
                \basename($this->configuration->getTargetDir()),
                \md5($this->configuration->getTargetDir()),
                \date('YmdHis'),
                $this->configuration->getTargetDir() // We're backing up this directory into BACKUP_DIR
            ));
        }

        if ($this->configuration->useComposer()) {
            $this->addCommand(\sprintf(
                'composer --no-ansi --no-interaction --no-progress --working-dir=%s install %s',
                $this->configuration->getTmpDir(),
                $this->configuration->getComposerOptions()
            ));

            if ($this->configuration->getComposerHome() !== null && \is_dir($this->configuration->getComposerHome())) {
                \putenv(\sprintf('COMPOSER_HOME=%s', $this->configuration->getComposerHome()));
            }
        }

        $excludes = '';
        foreach ($this->configuration->getExcludes() as $exclude) {
            $excludes .= \sprintf(' --exclude=%s', $exclude);
        }

        $this->addCommand(\sprintf(
            'rsync -rltgoDzvO %s %s %s %s',
            $this->configuration->getTmpDir(),
            $this->configuration->getTargetDir(),
            $this->configuration->isDeleteFiles() ? '--delete-after' : '',
            $excludes
        ));

        if ($this->configuration->isCleanUp()) {
            $this->terminalItems['clean_up'] = new Command(\sprintf('rm -rf %s', $this->configuration->getTmpDir()));
        }

        foreach ($this->terminalItems as $item) {
            if (($item instanceof Command) === false) {
                continue;
            }

            $item->setContext($this);
        }
    }

    /**
     * Initiate deployment commands for tmp_dir.
     *
     * @return void
     */
    private function initTmpDirCommands(): void
    {
        if (\is_dir($this->configuration->getTmpDir()) === false) {
            $this->addCommand(\sprintf(
                'git clone --depth=1 --branch %s %s %s',
                $this->configuration->getBranch(),
                $this->configuration->getRepository(),
                $this->configuration->getTmpDir()
            ));

            return;
        }

        $this
            ->addCommand(\sprintf(
                'git --git-dir="%s.git" --work-tree="%s" fetch --tags origin %s',
                $this->configuration->getTmpDir(),
                $this->configuration->getTmpDir(),
                $this->configuration->getBranch()
            ))
            ->addCommand(\sprintf(
                'git --git-dir="%s.git" --work-tree="%s" reset --hard FETCH_HEAD',
                $this->configuration->getTmpDir(),
                $this->configuration->getTmpDir()
            ));
    }

    /**
     * Reset the time limit for each command.
     *
     * @return void
     */
    private function resetTimeLimit(): void
    {
        \set_time_limit($this->configuration->getTimeLimit());
    }

    /**
     * Ensure that we're in the right directory if tmp dir valid.
     *
     * @return void
     */
    private function useTmpDir(): void
    {
        if (\file_exists($this->configuration->getTmpDir()) && \is_dir($this->configuration->getTmpDir())) {
            \chdir($this->configuration->getTmpDir()); //
        }
    }
}
