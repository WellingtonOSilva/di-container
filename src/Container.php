<?php

namespace WellingtonOSilva\DiContainer;

use Closure;
use Exception;
use ReflectionClass;

class Container
{
    private array $definitions = [];
    private array $instances = [];

    private static ?Container $instance = null;

    private function __construct() {}

    public static function getInstance(): Container {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function register($name, $definition)
    {
        $this->definitions[$name] = $definition;
    }

    public function simpleRegister($definition)
    {
        $this->definitions[$definition] = $definition;
    }


    public function get($name): object
    {
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        if (!isset($this->definitions[$name])) {
            throw new Exception("Service not found: " . $name);
        }

        $definition = $this->definitions[$name];
        if ($definition instanceof Closure) {
            $service = $definition($this);
        } elseif (is_string($definition) && class_exists($definition)) {
            $service = $this->autoWire($definition);
        } else {
            throw new Exception("Invalid definition for: " . $name);
        }

        $this->instances[$name] = $service;
        return $service;
    }

    private function autoWire($class)
    {
        $reflector = new ReflectionClass($class);
        if (!$reflector->isInstantiable()) {
            throw new Exception("Class $class is not instantiable");
        }

        $constructor = $reflector->getConstructor();
        if (is_null($constructor)) {
            return new $class;
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];
        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();
            if ($dependency) {
                $dependencies[] = $this->get($dependency->name);
            } else {
                throw new Exception("Cannot resolve the dependency: " . $parameter->name);
            }
        }

        return $reflector->newInstanceArgs($dependencies);
    }

    public function resolve($class)
    {
        if (isset($this->instances[$class])) {
            return $this->instances[$class];
        }

        if (class_exists($class)) {
            $service = $this->autoWire($class);
            $this->instances[$class] = $service;
            return $service;
        } else {
            throw new Exception("Class not found: " . $class);
        }
    }

}