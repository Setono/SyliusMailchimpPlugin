<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\Exception;

use PhpSpec\ObjectBehavior;
use Setono\SyliusMailchimpPlugin\Exception\NotSetUpException;

final class NotSetUpExceptionSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(NotSetUpException::class);
    }

    function it_is_an_exception(): void
    {
        $this->shouldHaveType(\Exception::class);
    }

    function it_has_custom_message(): void
    {
        $this->getMessage()->shouldReturn('Please set up the Mailchimp config properly first.');
    }
}
