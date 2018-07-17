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
    private $accessToken;

    /**
     * @var string
     */
    private $header;

    /**
     * @var null|\Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * PermissionChecker constructor.
     *
     * @param string $accessToken
     * @param string $header
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     */
    public function __construct(string $accessToken, string $header, RequestStack $requestStack)
    {
        $this->accessToken = $accessToken;
        $this->header = $header;
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * Check if current request can deploy.
     *
     * @return bool
     */
    public function isGranted(): bool
    {
        if ($this->request === null) {
            return false;
        }

        return $this->request->headers->get($this->header) === $this->accessToken
            || $this->request->get('access_token') === $this->accessToken;
    }
}
