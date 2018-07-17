<?php
declare(strict_types=1);

namespace App\Services\Deployment\Traits;

use App\Services\Deployment\Interfaces\ConfigurationInterface;
use App\Services\Deployment\Interfaces\ContextInterface;

trait ContextAwareTrait
{
    /**
     * @var \App\Services\Deployment\Interfaces\ContextInterface
     */
    private $context;

    /**
     * Set deployment context.
     *
     * @param \App\Services\Deployment\Interfaces\ContextInterface $context
     *
     * @return void
     */
    public function setContext(ContextInterface $context): void
    {
        $this->context = $context;
    }

    /**
     * Add error into the deployment context.
     *
     * @param string $description
     *
     * @return void
     */
    private function addError(string $description): void
    {
        $this->context->addError($description);
    }

    /**
     * Get configuration.
     *
     * @return \App\Services\Deployment\Interfaces\ConfigurationInterface
     */
    private function getConfiguration(): ConfigurationInterface
    {
        return $this->context->getConfiguration();
    }
}
