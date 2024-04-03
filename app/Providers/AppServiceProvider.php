<?php

namespace App\Providers;

use App\Util\DomainsDBValidator;
use App\Util\DomainValidator;
use App\Util\FakeDomainValidator;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->bindDomainValidator();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    public function bindDomainValidator(): void
    {
        $this->app->bind(DomainValidator::class, function () {
            $client = new Client([
                'base_uri' => config('services.domainsdb.url'),
                'redirect.disable' => true,
            ]);

            return new DomainsDBValidator($client);
        });
    }
}
