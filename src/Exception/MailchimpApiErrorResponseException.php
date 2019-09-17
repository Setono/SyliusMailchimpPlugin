<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Exception;

use Throwable;

class MailchimpApiErrorResponseException extends MailchimpApiException
{
    public function __construct(array $response, $code = 0, Throwable $previous = null)
    {
        $message = sprintf(
            '%s: %s (%s)',
            $response['title'],
            $response['detail'],
            $response['errors'] ? $this->inlineErrors($response['errors']) : ''
        );

        parent::__construct($message, $code, $previous);
    }

    private function inlineErrors(array $errors): string
    {
        return implode(', ', array_map(function($error){
            return sprintf(
                '%s: %s',
                $error['field'],
                $error['message']
            );
        }, $errors));
    }
}
