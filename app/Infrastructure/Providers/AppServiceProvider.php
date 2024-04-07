<?php

namespace App\Infrastructure\Providers;

use App\Domain\Factory\ActivityFactory;
use App\Domain\Factory\ActivityFactoryInterface;
use App\Domain\Repository\ActivityRepository;
use App\Domain\Service\ActivityParser;
use App\Domain\Service\ActivityParserInterface;
use App\Domain\Service\HtmlParser;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->tag(HtmlParser::class, 'parser');
        $this->app->singleton(ActivityParserInterface::class, function ($app) {
            return new ActivityParser(iterator_to_array(app()->tagged('parser')));
        });

        $this->app->bind(ActivityFactoryInterface::class, ActivityFactory::class);

        $this->app->bind(ActivityRepository::class, \App\Infrastructure\Database\ActivityRepository::class);

    }
}
