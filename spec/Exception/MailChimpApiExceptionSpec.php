<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailChimpPlugin\Exception;

use PhpSpec\ObjectBehavior;
use Setono\SyliusMailChimpPlugin\Exception\MailChimpApiException;

class MailChimpApiExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MailChimpApiException::class);
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
