<?php
declare(strict_types=1);

namespace App\Services\Deployment;

use App\Services\Deployment\Interfaces\ConfigurationInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Configuration implements ConfigurationInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * Configuration constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $this->setConfig($config);
    }

    /**
     * Get all configuration as an associative array.
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->config;
    }

    /**
     * Return optional backup dir.
     *
     * @return null|string
     */
    public function getBackupDir(): ?string
    {
        return $this->config['backup_dir'];
    }

    /**
     * Return branch to clone.
     *
     * @return string
     */
    public function getBranch(): string
    {
        return $this->config['branch'];
    }

    /**
     * Return composer home.
     *
     * @return null|string
     */
    public function getComposerHome(): ?string
    {
        return $this->config['composer_home'];
    }

    /**
     * Return composer options.
     *
     * @return string
     */
    public function getComposerOptions(): string
    {
        return $this->config['composer_options'];
    }

    /**
     * Get env values.
     *
     * @return array
     */
    public function getEnv(): array
    {
        return $this->config['env'];
    }

    /**
     * Get excludes for rsync.
     *
     * @return array
     */
    public function getExcludes(): array
    {
        return $this->config['excludes'];
    }

    /**
     * Return remote repository to clone.
     *
     * @return string
     */
    public function getRepository(): string
    {
        return $this->config['repository'];
    }

    /**
     * Return list of required binaries.
     *
     * @return array
     */
    public function getRequiredBinaries(): array
    {
        return $this->config['required_binaries'];
    }

    /**
     * Return target dir to deploy.
     *
     * @return string
     */
    public function getTargetDir(): string
    {
        return $this->config['target_dir'];
    }

    /**
     * Return time limit for each command.
     *
     * @return int
     */
    public function getTimeLimit(): int
    {
        return $this->config['time_limit'];
    }

    /**
     * Return tmp dir.
     *
     * @return string
     */
    public function getTmpDir(): string
    {
        return $this->config['tmp_dir'];
    }

    /**
     * Return version file.
     *
     * @return string
     */
    public function getVersionFile(): string
    {
        return $this->config['version_file'];
    }

    /**
     * Determine if clean up after proceed.
     *
     * @return bool
     */
    public function isCleanUp(): bool
    {
        return $this->config['clean_up'];
    }

    /**
     * Determine if delete files after proceed.
     *
     * @return bool
     */
    public function isDeleteFiles(): bool
    {
        return $this->config['delete_files'];
    }

    /**
     * Determine if deployment uses composer.
     *
     * @return bool
     */
    public function useComposer(): bool
    {
        return $this->config['use_composer'];
    }

    /**
     * Set configuration values.
     *
     * @param array $config
     *
     * @return array
     */
    private function setConfig(array $config): array
    {
        $optionsResolver = new OptionsResolver();

        // Required configurations
        $optionsResolver->setDefined(['repository', 'target_dir']);
        $optionsResolver->setRequired(['repository', 'target_dir']);

        // Defaults
        $optionsResolver->setDefaults([
            'backup_dir' => null,
            'branch' => 'master',
            'clean_up' => true,
            'composer_home' => null,
            'composer_options' => '--no-dev',
            'delete_files' => false,
            'env' => [],
            'excludes' => ['.git'],
            'required_binaries' => ['git', 'rsync'],
            'use_composer' => false,
            'time_limit' => 30
        ]);
        $optionsResolver->setDefault('tmp_dir', function (Options $options): string {
            return \sprintf('/tmp/deploy-%s/', \md5($options['repository']));
        });
        $optionsResolver->setDefault('version_file', function (Options $options): string {
            return \sprintf('%sVERSION', $options['tmp_dir']);
        });

        // Allowed types
        $optionsResolver->setAllowedTypes('backup_dir', ['null', 'string']);
        $optionsResolver->setAllowedTypes('branch', 'string');
        $optionsResolver->setAllowedTypes('clean_up', 'bool');
        $optionsResolver->setAllowedTypes('composer_home', ['null', 'string']);
        $optionsResolver->setAllowedTypes('composer_options', 'string');
        $optionsResolver->setAllowedTypes('delete_files', 'bool');
        $optionsResolver->setAllowedTypes('env', 'array');
        $optionsResolver->setAllowedTypes('excludes', 'array');
        $optionsResolver->setAllowedTypes('repository', 'string');
        $optionsResolver->setAllowedTypes('required_binaries', 'array');
        $optionsResolver->setAllowedTypes('use_composer', 'bool');
        $optionsResolver->setAllowedTypes('target_dir', 'string');
        $optionsResolver->setAllowedTypes('time_limit', 'int');
        $optionsResolver->setAllowedTypes('tmp_dir', 'string');
        $optionsResolver->setAllowedTypes('version_file', 'string');

        return $optionsResolver->resolve($config);
    }
}
