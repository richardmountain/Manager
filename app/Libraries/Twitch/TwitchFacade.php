<?php 

namespace App\Libraries\Twitch;

use \Illuminate\Support\Facades\Facade;

class TwitchFacade extends Facade {

    /**
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'Twitch'; }

}