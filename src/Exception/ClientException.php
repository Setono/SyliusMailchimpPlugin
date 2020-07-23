<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Exception;

use RuntimeException;
use Safe\Exceptions\JsonException;
use function Safe\json_decode;

class ClientException extends RuntimeException implements Exception
{
    /** @var int */
    private $statusCode;

    /**
     * Returns an array like:
     *
     * [
     *     0 => [
     *         'field' => 'field name',
     *         'message' => 'error message'
     *     ]
     * ]
     *
     * @var array
     */
    private $errors = [];

    /**
     * @param array $lastResponse The response from the Mailchimp HTTP cloent
     */
    public function __construct(array $lastResponse)
    {
        $this->parseHeaders($lastResponse);
        $message = $this->parseBody($lastResponse);

        parent::__construct($message);
    }

    private function parseHeaders(array $response): void
    {
        if (!isset($response['headers']['http_code'])) {
            return;
        }

        $this->statusCode = (int) $response['headers']['http_code'];
    }

    /**
     * @return string The exception message
     */
    private function parseBody(array $response): string
    {
        if (!isset($response['body'])) {
            return 'No body on the response.';
        }

        $body = json_decode($response['body'], true);

        if (isset($body['errors']) && is_array($body['errors'])) {
            $this->errors = $body['errors'];
        }

        return $body['title'] . ': ' . $body['detail'];
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
