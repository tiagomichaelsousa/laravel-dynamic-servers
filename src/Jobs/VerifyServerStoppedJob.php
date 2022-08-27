<?php

namespace Spatie\DynamicServers\Jobs;

use Exception;
use Spatie\DynamicServers\Actions\MarkServerAsReadyAction;
use Spatie\DynamicServers\Events\ServerRunningEvent;
use Spatie\DynamicServers\Events\ServerStoppedEvent;
use Spatie\DynamicServers\Models\Server;
use Spatie\DynamicServers\Support\Config;
use Spatie\DynamicServers\UpCloud;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\DynamicServers\Enums\ServerStatus;

class VerifyServerStoppedJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $deleteWhenMissingModels = true;

    public function __construct(public Server $server)
    {
    }

    public function handle(): bool
    {
        try {
            if ($this->server->provider()->hasBeenStopped()) {

                $this->server->markAs(ServerStatus::Stopped);

                event(new ServerStoppedEvent($this->server));

                $deleteServerJob = Config::jobClass('delete_server');

                dispatch(new $deleteServerJob($this->server));

                return;
            }

            $this->release(60);
        } catch (Exception $exception) {
            $this->server->markAsErrored($exception);

            report($exception);
        }
    }
}
