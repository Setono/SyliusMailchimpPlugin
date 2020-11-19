<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Throwable;

trait MailchimpAwareTrait
{
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $mailchimpState = self::MAILCHIMP_STATE_PENDING;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $mailchimpError;

    /**
     * @var Throwable|null
     *
     * @ORM\Column(type="object", nullable=true)
     */
    protected $mailchimpException;

    /**
     * @var DateTimeInterface|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $mailchimpStateUpdatedAt;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $mailchimpTries = 0;

    public function getMailchimpState(): string
    {
        return $this->mailchimpState;
    }

    public function setMailchimpState(string $mailchimpState): void
    {
        $this->mailchimpState = $mailchimpState;
    }

    public function getMailchimpError(): ?string
    {
        return $this->mailchimpError;
    }

    public function setMailchimpError(?string $mailchimpError): void
    {
        $this->mailchimpError = $mailchimpError;
    }

    public function getMailchimpException(): ?Throwable
    {
        return $this->mailchimpException;
    }

    public function setMailchimpException(?Throwable $mailchimpException): void
    {
        $this->mailchimpException = $mailchimpException;
    }

    public function getMailchimpStateUpdatedAt(): ?DateTimeInterface
    {
        return $this->mailchimpStateUpdatedAt;
    }

    public function setMailchimpStateUpdatedAt(DateTimeInterface $mailchimpStateUpdatedAt): void
    {
        $this->mailchimpStateUpdatedAt = $mailchimpStateUpdatedAt;
    }

    public function getMailchimpTries(): int
    {
        return $this->mailchimpTries;
    }

    public function setMailchimpTries(int $mailchimpTries): void
    {
        $this->mailchimpTries = $mailchimpTries;
    }
}
