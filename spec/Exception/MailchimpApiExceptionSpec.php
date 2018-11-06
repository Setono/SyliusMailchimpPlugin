<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\Exception;

use PhpSpec\ObjectBehavior;
use Setono\SyliusMailchimpPlugin\Exception\MailchimpApiException;

class MailchimpApiExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MailchimpApiException::class);
    }

    function let(): void
    {
        $this->beConstructedWith('message');
    }

    function it_is_an_exception(): void
    {
        $this->shouldHaveType(\Exception::class);
    }

    function it_has_custom_message(): void
    {
        $this->getMessage()->shouldReturn('message');
    }
}
