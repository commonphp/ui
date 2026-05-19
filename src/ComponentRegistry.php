<?php

declare(strict_types=1);

namespace CommonPHP\UI;

use CommonPHP\UI\Contracts\ComponentInterface;
use CommonPHP\UI\Contracts\ComponentRegistryInterface;
use CommonPHP\UI\Exceptions\InvalidComponentException;

class ComponentRegistry implements ComponentRegistryInterface
{
    /**
     * @var array<string, ComponentInterface>
     */
    private array $components = [];

    /**
     * @param iterable<ComponentInterface> $components
     */
    public function __construct(iterable $components = [])
    {
        foreach ($components as $component) {
            $this->register($component);
        }
    }

    public function register(ComponentInterface $component): static
    {
        $this->components[$this->normalizeName($component->componentName())] = $component;

        return $this;
    }

    /**
     * @param array<string, mixed>|ViewData $data
     */
    public function set(string $name, ComponentInterface|string $component, array|ViewData $data = []): static
    {
        return $this->register(
            $component instanceof ComponentInterface ? $component : Component::fromTemplate($name, $component, $data),
        );
    }

    public function has(string $name): bool
    {
        return array_key_exists($this->normalizeName($name), $this->components);
    }

    public function get(string $name): ComponentInterface
    {
        $name = $this->normalizeName($name);

        if (!$this->has($name)) {
            throw InvalidComponentException::notFound($name);
        }

        return $this->components[$name];
    }

    public function remove(string $name): static
    {
        unset($this->components[$this->normalizeName($name)]);

        return $this;
    }

    public function all(): array
    {
        return $this->components;
    }

    /**
     * @return list<string>
     */
    public function names(): array
    {
        return array_keys($this->components);
    }

    private function normalizeName(string $name): string
    {
        $name = trim($name);

        if ($name === '') {
            throw InvalidComponentException::invalidName();
        }

        return $name;
    }
}
