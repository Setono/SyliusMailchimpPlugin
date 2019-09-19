<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

trait PushedToMailchimpAwareTrait
{
    /**
     * @var DateTimeInterface|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $pushedToMailchimp;

    public function isPushedToMailchimp(): bool
    {
        return null !== $this->pushedToMailchimp;
    }

    public function getPushedToMailchimp(): ?DateTimeInterface
    {
        return $this->pushedToMailchimp;
    }

    public function setPushedToMailchimp(DateTimeInterface $dateTime = null): void
    {
        if (null === $dateTime) {
            $dateTime = new DateTime();
        }

        $this->pushedToMailchimp = $dateTime;
    }
}
