<?php

namespace Framework\Facade;

use Framework\Foundation\Mapping\{Endpoint, Rule};

class Map
{
    public function rule(string $path, string $name): Rule
    {
        return new Rule($path, $name);
    }

    public function endpoint(string $name, string $class, null|string $method = null): Endpoint
    {
        return new Endpoint($name, $class, $method);
    }
}
