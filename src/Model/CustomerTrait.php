<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait CustomerTrait
{
    /**
     * @var DateTimeInterface|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $lastMailchimpSync;

    public function getLastMailchimpSync(): ?DateTimeInterface
    {
        return $this->lastMailchimpSync;
    }

    public function setLastMailchimpSync(DateTimeInterface $dateTime = null): void
    {
        if (null === $dateTime) {
            $dateTime = new DateTime();
        }

        $this->lastMailchimpSync = $dateTime;
    }
}
