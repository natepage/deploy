<?php
declare(strict_types=1);

namespace App\Services\Security;

class GithubPermissionChecker extends PermissionChecker
{
    private const HEADER = 'X-Hub-Signature';

    /**
     * {@inheritdoc}
     */
    public function isGranted(): bool
    {
        return parent::isGranted() || $this->isHubSignatureValid();
    }

    /**
     * Check if the X-Hub-Signature header is provided and valid.
     *
     * @return bool
     */
    private function isHubSignatureValid(): bool
    {
        $header = $this->request->headers->get(self::HEADER);

        if ($header === null) {
            return false;
        }

        return $header === \hash_hmac('sha1', $this->request->getContent(), $this->accessToken);
    }
}
