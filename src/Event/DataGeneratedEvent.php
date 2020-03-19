<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Event;

use InvalidArgumentException;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;
use Spatie\DataTransferObject\DataTransferObject;
use Symfony\Contracts\EventDispatcher\Event;

abstract class DataGeneratedEvent extends Event
{
    /** @var DataTransferObject */
    private $data;

    /** @var array */
    private $context;

    public function __construct(DataTransferObject $data, array $context = [])
    {
        $this->data = $data;
        $this->context = $context;
    }

    public function getData(): DataTransferObject
    {
        return $this->data;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function hasContextKey(string $key): bool
    {
        return array_key_exists($key, $this->context);
    }

    /**
     * @return mixed
     *
     * @throws StringsException
     */
    public function getContextItem(string $key)
    {
        if (!$this->hasContextKey($key)) {
            throw new InvalidArgumentException(
                sprintf('The key "%s" does not exist in the context array', $key)
            );
        }

        return $this->context[$key];
    }
}
