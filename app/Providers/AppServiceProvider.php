<?php

namespace App\Providers;

use App\Services\Contracts\ProductSearchServiceInterface;
use App\Services\ProductSearchService;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Client::class, function () {
            return ClientBuilder::create()
                ->setHosts([Config::get('services.elasticsearch.host', 'http://localhost:9200')])
                ->build();
        });

        $this->app->singleton(ProductSearchServiceInterface::class, ProductSearchService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
