<?php

namespace WellingtonOSilva\DiContainer;



use WellingtonOSilva\DiContainer\Contracts\Provider;

class App
{

    /**
     * @var Provider[]
     */
    private array $providers;

    /**
     * @param class-string<Provider>$provider
     */
    public final function withProvider($provider): App {
        $this->providers[] = $provider;
        return $this;
    }


    public final function run(\Closure $closure) {
        $this->bootProviders();
        return $closure(Container::getInstance());
    }

    private function bootProviders(): void {

        if(empty($this->providers)) {
            $this->defaultProvider();
            return;
        }

        foreach ($this->providers as $provider) {
            $instance =  new $provider();
            $instance->setup(Container::getInstance());
        }
    }

    private function defaultProvider(): void {
        (new DefaultProvider())
            ->setup(Container::getInstance());
    }


}