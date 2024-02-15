<?php

declare(strict_types=1);

namespace support\Providers;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use  support\Providers\Concerns\InteractsWithApplication;

/**
 * Class     ServiceProvider
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class ServiceProvider extends IlluminateServiceProvider
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use InteractsWithApplication;
}
