<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Libraries\Twitch\Twitch;

use \jofner\SDK\TwitchTV\TwitchSDK;

class TwitchServiceProvider extends ServiceProvider
{
    private $twitch;
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $twitch_config = array(
        'client_id' => env('TWITCH_KEY'),
        'client_secret' => env('TWITCH_SECRET'),
        'redirect_uri' => env('TWITCH_REDIRECT_URI'),
        );
        $this->twitch = new TwitchSDK($twitch_config);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Twitch', function () {
            return new Twitch($this->twitch);
        });
    }
}
