<?php

return [

    /*
     * Redirect url after login
     */
    'redirect_url' => '/steamlogin',
    /*
     *  Api Key (http://steamcommunity.com/dev/apikey)
     */
    'api_key' => env('STEAM_API_KEY'),
    /*
     * Is using https?
     */
    'https' => true

];
