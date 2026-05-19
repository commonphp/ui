<?php

declare(strict_types=1);

namespace CommonPHP\UI\Contracts;

interface ComponentRegistryInterface
{
    public function register(ComponentInterface $component): static;

    public function has(string $name): bool;

    public function get(string $name): ComponentInterface;

    public function remove(string $name): static;

    /**
     * @return array<string, ComponentInterface>
     */
    public function all(): array;
}
