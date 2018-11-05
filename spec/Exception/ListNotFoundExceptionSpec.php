<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailChimpPlugin\Exception;

use PhpSpec\ObjectBehavior;
use Setono\SyliusMailChimpPlugin\Exception\ListNotFoundException;

class ListNotFoundExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
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
        $this->getMessage()->shouldReturn('MailChimp list with 123 code has not been found.');
    }
}
