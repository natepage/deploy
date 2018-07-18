<?php
declare(strict_types=1);

namespace App\Services\Deployment\Traits;

trait CanCountTime
{
    /**
     * @var float
     */
    private $duration;

    /**
     * @var float
     */
    private $end;

    /**
     * @var float
     */
    private $start;

    /**
     * Get last duration.
     *
     * @return float|null
     */
    public function getDuration(): float
    {
        return $this->duration ?? -1;
    }

    /**
     * Get last end.
     *
     * @return float|null
     */
    public function getEnd(): ?float
    {
        return $this->end;
    }

    /**
     * Get last start.
     *
     * @return float|null
     */
    public function getStart(): ?float
    {
        return $this->start;
    }

    /**
     * Start counting time and reset duration.
     *
     * @return void
     */
    protected function startCounting(): void
    {
        $this->start = \microtime(true);
        $this->duration = -1;
    }

    /**
     * Stop counting time and calculate duration.
     *
     * @return void
     */
    protected function stopCounting(): void
    {
        $this->end = \microtime(true);
        $this->duration = $this->end - $this->start;
    }
}
