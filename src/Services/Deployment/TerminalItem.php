<?php
declare(strict_types=1);

namespace App\Services\Deployment;

use App\Services\Deployment\Interfaces\TerminalItemInterface;
use App\Services\Deployment\Traits\CanCountTime;

class TerminalItem implements TerminalItemInterface
{
    use CanCountTime;

    /**
     * @var string
     */
    protected $input;

    /**
     * @var array
     */
    protected $output;

    /**
     * @var string
     */
    protected $uniqueId;

    /**
     * @var bool
     */
    protected $error;

    /**
     * TerminalItem constructor.
     *
     * @param string $input
     * @param array|null $output
     * @param bool|null $error
     */
    public function __construct(string $input, ?array $output = null, ?bool $error = null)
    {
        $this->input = $input;
        $this->output = $output ?? [];
        $this->error = $error ?? false;
        $this->uniqueId = \uniqid('command', false);
    }

    /**
     * Get terminal item input.
     *
     * @return string
     */
    public function getInput(): string
    {
        return $this->input;
    }

    /**
     * Get terminal item output.
     *
     * @return array
     */
    public function getOutput(): array
    {
        return $this->output;
    }

    /**
     * Get terminal item unique id.
     *
     * @return string
     */
    public function getUniqueId(): string
    {
        return $this->uniqueId;
    }

    /**
     * Determine if terminal item had error.
     *
     * @return bool
     */
    public function isError(): bool
    {
        return $this->error;
    }
}
