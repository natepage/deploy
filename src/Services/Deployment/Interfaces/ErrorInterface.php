<?php
declare(strict_types=1);

namespace App\Services\Deployment\Interfaces;

interface ErrorInterface
{
    /**
     * Return error description.
     *
     * @return string
     */
    public function getDescription(): string;
}
