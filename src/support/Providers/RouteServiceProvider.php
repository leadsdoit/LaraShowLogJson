<?php

declare(strict_types=1);

namespace support\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as IlluminateServiceProvider;
use  support\Routing\Concerns\RegistersRouteClasses;

/**
 * Class     RouteServiceProvider
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class RouteServiceProvider extends IlluminateServiceProvider
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use RegistersRouteClasses;
}
