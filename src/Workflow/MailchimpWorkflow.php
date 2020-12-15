<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Workflow;

/**
 * This class has constants for the 'mailchimp' workflow
 */
final class MailchimpWorkflow
{
    public const NAME = 'mailchimp';

    public const TRANSITION_PROCESS = 'process';

    public const TRANSITION_PUSH = 'push';

    public const TRANSITION_FAIL = 'fail';

    public const TRANSITION_FAIL_TERMINALLY = 'fail_terminally';

    public const TRANSITION_RETRY = 'retry';

    public const TRANSITION_UPDATE = 'update';

    private function __construct()
    {
    }
}
