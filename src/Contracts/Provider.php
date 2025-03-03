<?php

namespace WellingtonOSilva\DiContainer\Contracts;


use WellingtonOSilva\DiContainer\Container;

interface Provider
{
    public function setup(Container $container);

}