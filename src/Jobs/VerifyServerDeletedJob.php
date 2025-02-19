<?php

namespace Spatie\DynamicServers\Jobs;

use Exception;
use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Events\ServerDeletedEvent;

class VerifyServerDeletedJob extends DynamicServerJob
{
    public function handle()
    {
        try {
            if ($this->server->provider()->hasBeenDeleted()) {
                $this->server->markAs(ServerStatus::Deleted);

                event(new ServerDeletedEvent($this->server));

                return;
            }

            $this->release(20);
        } catch (Exception $exception) {
            $this->server->markAsErrored($exception);

            report($exception);
        }
    }

    public function retryUntil()
    {
        return now()->addMinutes(10);
    }
}
