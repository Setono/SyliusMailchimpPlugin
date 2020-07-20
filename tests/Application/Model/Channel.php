<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusMailchimpPlugin\Application\Model;

use Sylius\Component\Core\Model\Channel as BaseChannel;
use Setono\SyliusMailchimpPlugin\Model\ChannelInterface as SetonoSyliusMailchimpPluginChannelInterface;
use Setono\SyliusMailchimpPlugin\Model\ChannelTrait as SetonoSyliusMailchimpPluginChannelTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_channel")
 */
class Channel extends BaseChannel implements SetonoSyliusMailchimpPluginChannelInterface
{
    use SetonoSyliusMailchimpPluginChannelTrait;
}
