<?php
declare(strict_types=1);

namespace App\Services\Deployment\Interfaces;

use DateTime;

interface ContextInterface
{
    /**
     * Add error to deployment context.
     *
     * @param string $description
     *
     * @return \App\Services\Deployment\Interfaces\ContextInterface
     */
    public function addError(string $description): self;

    /**
     * Trigger the deployment.
     *
     * @return void
     */
    public function deploy(): void;

    /**
     * Return list of deployment commands.
     *
     * @return array
     */
    public function getCommands(): array;

    /**
     * Return deployment configuration.
     *
     * @return \App\Services\Deployment\Interfaces\ConfigurationInterface
     */
    public function getConfiguration(): ConfigurationInterface;

    /**
     * Return deployment date.
     *
     * @return \DateTime
     */
    public function getDate(): DateTime;

    /**
     * Return deployment duration.
     *
     * @return float
     */
    public function getDuration(): float;

    /**
     * Return list of deployment errors.
     *
     * @return array
     */
    public function getErrors(): array;

    /**
     * Return deployment system.
     *
     * @return \App\Services\Deployment\Interfaces\SystemInterface
     */
    public function getSystem(): SystemInterface;
}
