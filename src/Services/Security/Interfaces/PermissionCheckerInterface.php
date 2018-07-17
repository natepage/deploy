<?php
declare(strict_types=1);

namespace App\Services\Security\Interfaces;

interface PermissionCheckerInterface
{
    /**
     * Check if current request can deploy.
     *
     * @return bool
     */
    public function isGranted(): bool;
}
