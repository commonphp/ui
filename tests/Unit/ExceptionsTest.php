<?php

declare(strict_types=1);

namespace CommonPHP\UI\Tests\Unit;

use CommonPHP\UI\Exceptions\InvalidComponentException;
use CommonPHP\UI\Exceptions\RenderException;
use CommonPHP\UI\Exceptions\RendererDriverException;
use CommonPHP\UI\Exceptions\TemplateNotFoundException;
use CommonPHP\UI\Exceptions\UIException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

final class ExceptionsTest extends TestCase
{
    public function testUIExceptionIsTheBasePackageException(): void
    {
        $exception = new UIException('UI failed.');

        self::assertInstanceOf(RuntimeException::class, $exception);
        self::assertSame('UI failed.', $exception->getMessage());
    }

    public function testInvalidComponentExceptionFactoriesDescribeTheProblem(): void
    {
        self::assertSame('Component names cannot be empty.', InvalidComponentException::invalidName()->getMessage());
        self::assertSame(
            'Component "badge" is not registered.',
            InvalidComponentException::notFound('badge')->getMessage(),
        );
    }

    public function testTemplateNotFoundExceptionCanIncludeSearchedPaths(): void
    {
        self::assertSame(
            'Template "pages.missing" was not found.',
            TemplateNotFoundException::forTemplate('pages.missing')->getMessage(),
        );
        self::assertSame(
            'Template "pages.missing" was not found. Searched: one.php, two.php.',
            TemplateNotFoundException::forTemplate('pages.missing', ['one.php', 'two.php'])->getMessage(),
        );
    }

    public function testRenderExceptionCarriesPreviousThrowable(): void
    {
        $previous = new RuntimeException('exploded');
        $exception = RenderException::forTemplate('pages.profile', $previous);

        self::assertSame('Unable to render template "pages.profile": exploded', $exception->getMessage());
        self::assertSame($previous, $exception->getPrevious());
    }

    public function testRendererDriverExceptionFactoriesDescribeDriverFailures(): void
    {
        $previous = new RuntimeException('driver exploded');
        $operation = RendererDriverException::forOperation('render', $previous);

        self::assertSame('Expected a UI renderer driver, got stdClass.', RendererDriverException::invalidDriver(new stdClass())->getMessage());
        self::assertSame('Renderer driver failed during render: driver exploded', $operation->getMessage());
        self::assertSame($previous, $operation->getPrevious());
    }
}
