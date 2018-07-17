<?php
declare(strict_types=1);

namespace App\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Deployment
{
    /**
     * @ORM\Column(name="context", type="text")
     *
     * @var string
     */
    private $context;

    /**
     * @ORM\Column(name="date", type="datetime")
     *
     * @var \DateTime
     */
    private $date;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id", type="integer")
     *
     * @var int
     */
    private $deploymentId;

    /**
     * @ORM\Column(name="duration", type="float", precision=2)
     *
     * @var float
     */
    private $duration;

    /**
     * @ORM\Column(name="status", type="boolean")
     *
     * @var bool
     */
    private $status;

    /**
     * Get deployment context.
     *
     * @return null|string
     */
    public function getContext(): ?string
    {
        return $this->context;
    }

    /**
     * Get deployment date.
     *
     * @return \DateTime|null
     */
    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    /**
     * Get deployment duration.
     *
     * @return float|null
     */
    public function getDuration(): ?float
    {
        return $this->duration;
    }

    /**
     * Get deployment id.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->deploymentId;
    }

    /**
     * Get deployment status.
     *
     * @return bool|null
     */
    public function getStatus(): ?bool
    {
        return $this->status;
    }

    /**
     * Set deployment context.
     *
     * @param string $context
     *
     * @return \App\Database\Entity\Deployment
     */
    public function setContext(string $context): self
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Set deployment date.
     *
     * @param \DateTime $date
     *
     * @return \App\Database\Entity\Deployment
     */
    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Set deployment duration.
     *
     * @param float $duration
     *
     * @return \App\Database\Entity\Deployment
     */
    public function setDuration(float $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Set deployment status.
     *
     * @param bool $status
     *
     * @return \App\Database\Entity\Deployment
     */
    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }
}
