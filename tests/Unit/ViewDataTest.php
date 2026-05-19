<?php

declare(strict_types=1);

namespace CommonPHP\UI\Tests\Unit;

use CommonPHP\UI\ViewData;
use PHPUnit\Framework\TestCase;

final class ViewDataTest extends TestCase
{
    public function testFromAcceptsNullArraysAndExistingInstances(): void
    {
        $empty = ViewData::from();
        $fromArray = ViewData::from(['title' => 'Dashboard']);

        self::assertTrue($empty->isEmpty());
        self::assertSame('Dashboard', $fromArray->get('title'));
        self::assertSame($fromArray, ViewData::from($fromArray));
    }

    public function testItStoresReadsAndChecksDirectAndNestedData(): void
    {
        $data = new ViewData([
            'title' => 'Dashboard',
            'profile.email' => 'direct@example.com',
            'profile' => [
                'name' => 'Ada',
                'email' => null,
                'optional' => null,
            ],
        ]);

        self::assertTrue($data->has('title'));
        self::assertTrue($data->has('profile.name'));
        self::assertTrue($data->has('profile.email'));
        self::assertFalse($data->has('profile.missing'));
        self::assertSame('direct@example.com', $data->get('profile.email'));
        self::assertSame('Ada', $data->get('profile.name'));
        self::assertNull($data->get('profile.optional', 'fallback'));
        self::assertSame('fallback', $data->get('missing', 'fallback'));
    }

    public function testItMutatesWithSetRemoveReplaceMergeAndClear(): void
    {
        $data = new ViewData(['title' => 'Original']);

        self::assertSame($data, $data->set('count', 3));
        self::assertSame($data, $data->merge(['title' => 'Merged', 'status' => 'ready']));
        self::assertSame('Merged', $data->get('title'));
        self::assertSame(3, $data->get('count'));
        self::assertSame('ready', $data->get('status'));

        self::assertSame($data, $data->remove('count'));
        self::assertFalse($data->has('count'));

        self::assertSame($data, $data->replace(['fresh' => true]));
        self::assertSame(['fresh' => true], $data->all());

        self::assertSame($data, $data->clear());
        self::assertTrue($data->isEmpty());
    }

    public function testWithMethodsReturnClonedDataBags(): void
    {
        $data = new ViewData(['title' => 'Original', 'status' => 'draft']);
        $clone = $data
            ->with('title', 'Clone')
            ->withMerged(['count' => 5])
            ->without('status');

        self::assertSame(['title' => 'Original', 'status' => 'draft'], $data->all());
        self::assertSame(['title' => 'Clone', 'count' => 5], $clone->all());
    }

    public function testItSupportsArrayAccessIterationCountingAndJsonSerialization(): void
    {
        $data = new ViewData(['title' => null]);

        self::assertTrue(isset($data['title']));
        self::assertNull($data['title']);

        $data['status'] = 'ready';
        $data[] = 'numeric';
        unset($data['title']);

        self::assertFalse(isset($data['title']));
        self::assertNull($data[0]);
        self::assertSame(2, count($data));
        self::assertSame(['status' => 'ready', 0 => 'numeric'], iterator_to_array($data));
        self::assertSame(['status' => 'ready', 0 => 'numeric'], $data->jsonSerialize());
    }

    public function testItCanMergeAnotherViewDataObject(): void
    {
        $data = new ViewData(['title' => 'Original']);
        $other = new ViewData(['title' => 'Other', 'subtitle' => 'Details']);

        $data->merge($other);

        self::assertSame(['title' => 'Other', 'subtitle' => 'Details'], $data->all());
    }
}
