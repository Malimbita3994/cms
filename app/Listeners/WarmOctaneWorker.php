<?php

namespace App\Listeners;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Laravel\Octane\Events\WorkerStarting;
use Throwable;

/**
 * Pre-handle lightweight routes when an Octane worker boots so the first real
 * admin request does not pay the full Laravel + Filament bootstrap cost.
 */
class WarmOctaneWorker
{
    /** @var list<string> */
    private const WARM_PATHS = ['/up', '/'];

    public function __construct(private readonly Kernel $kernel) {}

    public function handle(WorkerStarting $event): void
    {
        foreach (self::WARM_PATHS as $path) {
            try {
                $request = Request::create($path, 'GET');
                $response = $this->kernel->handle($request);
                $this->kernel->terminate($request, $response);
            } catch (Throwable) {
                // Database or env may be unavailable during boot; ignore.
            }
        }
    }
}
