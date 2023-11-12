<?php

namespace App\Providers;

use App\Services\Helpers\EzMoney;
use App\Services\Swap\HnbService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Component;
use Swap\Service\Registry;


class  AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        App::bind('EzMoney',function() {
            return new EzMoney();
        });

        Builder::macro('search',function($field,$string){
            return $string ? $this->where($field,'like', '%'.$string.'%'): $this;
        });


        Queue::after(function (JobProcessed $event) {
           // DB::table('test')->insert(array('event'=>serialize($event->job->payload())));
            Log::channel('ez_mail')->debug('app service:');
        });

        Registry::register('hnb', HnbService::class);

        if(App::environment('production')){
            URL::forceScheme('https');
        }
    }
}
