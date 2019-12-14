<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Loader;

interface AudiencesLoaderInterface
{
    /**
     * @param bool $preserve Preserve audiences that no longer exists on Mailchimp's end
     */
    public function load(bool $preserve = false): void;
}
