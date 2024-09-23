<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\HasLifecycleCallbacks
 */
trait CurrentStatus
{
    #[ORM\Column(length: 63)]
    private string $status;

    #[ORM\PrePersist]
    public function onPrePersistStatus(): void
    {
        $this->status = 'NEW';
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}
