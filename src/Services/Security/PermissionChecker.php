<?php
declare(strict_types=1);

namespace App\Services\Security;

use App\Services\Security\Interfaces\PermissionCheckerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PermissionChecker implements PermissionCheckerInterface
{
    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var null|\Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * PermissionChecker constructor.
     *
     * @param string $accessToken
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     */
    public function __construct(string $accessToken, RequestStack $requestStack)
    {
        $this->accessToken = $accessToken;
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * Check if current request can deploy.
     *
     * @return bool
     */
    public function isGranted(): bool
    {
        return $this->request !== null && $this->request->get('access_token') === $this->accessToken;
    }
}
