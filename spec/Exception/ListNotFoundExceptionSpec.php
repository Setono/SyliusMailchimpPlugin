<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\Exception;

use PhpSpec\ObjectBehavior;
use Setono\SyliusMailchimpPlugin\Exception\ListNotFoundException;

final class ListNotFoundExceptionSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(ListNotFoundException::class);
    }

    function let(): void
    {
        $this->beConstructedWith('123');
    }

    function it_is_an_exception(): void
    {
        $this->shouldHaveType(\Exception::class);
    }

    function it_has_custom_message(): void
    {
        $this->getMessage()->shouldReturn('Mailchimp list with 123 code has not been found.');
    }
}
