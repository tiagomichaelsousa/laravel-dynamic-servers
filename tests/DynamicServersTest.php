<?php

use Spatie\DynamicServers\Exceptions\JobDoesNotExist;
use Spatie\DynamicServers\Support\Config;

it('can get a job class', function () {
    expect(Config::dynamicServerJobClass('create_server'))->toBe(\Spatie\DynamicServers\Jobs\CreateServerJob::class);
});

it('throws when adding an invalid job class', function () {
    $this->expectException(JobDoesNotExist::class);

    Config::dynamicServerJobClass('something-wrong');
});
