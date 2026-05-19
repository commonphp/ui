<?php

declare(strict_types=1);

namespace CommonPHP\UI\Contracts;

interface ComponentInterface extends TemplateInterface
{
    public function componentName(): string;
}
