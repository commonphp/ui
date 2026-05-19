<?php

declare(strict_types=1);

namespace CommonPHP\UI;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * @implements ArrayAccess<string, mixed>
 * @implements IteratorAggregate<string, mixed>
 */
final class ViewData implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        private array $data = [],
    ) {
    }

    /**
     * @param array<string, mixed>|self|null $data
     */
    public static function from(array|self|null $data = null): self
    {
        if ($data instanceof self) {
            return $data;
        }

        return new self($data ?? []);
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->data;
    }

    public function has(string $key): bool
    {
        if (array_key_exists($key, $this->data)) {
            return true;
        }

        return $this->readNested($key, existsOnly: true) === true;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        $missing = new \stdClass();
        $value = $this->readNested($key, $missing);

        return $value === $missing ? $default : $value;
    }

    public function set(string $key, mixed $value): static
    {
        $this->data[$key] = $value;

        return $this;
    }

    public function remove(string $key): static
    {
        unset($this->data[$key]);

        return $this;
    }

    /**
     * @param array<string, mixed>|self $data
     */
    public function replace(array|self $data): static
    {
        $this->data = self::from($data)->all();

        return $this;
    }

    /**
     * @param array<string, mixed>|self $data
     */
    public function merge(array|self $data): static
    {
        $this->data = array_replace($this->data, self::from($data)->all());

        return $this;
    }

    public function clear(): static
    {
        $this->data = [];

        return $this;
    }

    public function with(string $key, mixed $value): self
    {
        $clone = clone $this;
        $clone->set($key, $value);

        return $clone;
    }

    /**
     * @param array<string, mixed>|self $data
     */
    public function withMerged(array|self $data): self
    {
        $clone = clone $this;
        $clone->merge($data);

        return $clone;
    }

    public function without(string $key): self
    {
        $clone = clone $this;
        $clone->remove($key);

        return $clone;
    }

    public function isEmpty(): bool
    {
        return $this->data === [];
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->data);
    }

    public function jsonSerialize(): array
    {
        return $this->data;
    }

    public function offsetExists(mixed $offset): bool
    {
        return is_string($offset) && $this->has($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return is_string($offset) ? $this->get($offset) : null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!is_string($offset)) {
            $this->data[] = $value;
            return;
        }

        $this->set($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        if (is_string($offset)) {
            $this->remove($offset);
        }
    }

    private function readNested(string $key, mixed $missing = null, bool $existsOnly = false): mixed
    {
        if (!str_contains($key, '.')) {
            return $existsOnly ? false : $missing;
        }

        $value = $this->data;

        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $existsOnly ? false : $missing;
            }

            $value = $value[$segment];
        }

        return $existsOnly ? true : $value;
    }
}
