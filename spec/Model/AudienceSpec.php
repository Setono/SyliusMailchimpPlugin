<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\Model;

use PhpSpec\ObjectBehavior;
use Setono\SyliusMailchimpPlugin\Model\Audience;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Sylius\Component\Channel\Model\ChannelAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

final class AudienceSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(Audience::class);
    }

    function it_is_a_resource(): void
    {
        $this->shouldHaveType(ResourceInterface::class);
    }

    function it_implements_audience_interface(): void
    {
        $this->shouldImplement(AudienceInterface::class);
    }

    function it_implements_channel_aware_interface(): void
    {
        $this->shouldImplement(ChannelAwareInterface::class);
    }
}
