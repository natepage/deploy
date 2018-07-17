<?php
declare(strict_types=1);

namespace App\Services\Deployment;

use App\Services\Deployment\Interfaces\ErrorInterface;

class Error implements ErrorInterface
{
    /**
     * @var string
     */
    private $description;

    /**
     * Error constructor.
     *
     * @param string $description
     */
    public function __construct(string $description)
    {
        $this->description = $description;
    }

    /**
     * Return error description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
