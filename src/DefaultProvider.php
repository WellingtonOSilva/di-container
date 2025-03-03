<?php

namespace WellingtonOSilva\DiContainer;

use GuzzleHttp\Client;
use WellingtonOSilva\DiContainer\Contracts\Provider;

class DefaultProvider implements Provider
{

    public function setup(Container $container)
    {
        $container->register(Client::class, function () {
            return new Client();
        });
    }
}