<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Exception;

use RuntimeException;
use function Safe\json_decode;

class ClientException extends RuntimeException implements ExceptionInterface
{
    /** @var string */
    private $uri;

    /** @var array */
    private $options;

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
    public function __construct(string $uri, array $options, array $lastResponse)
    {
        $this->uri = $uri;
        $this->options = $options;
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

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getOptions(): array
    {
        return $this->options;
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
