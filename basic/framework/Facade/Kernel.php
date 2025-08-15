<?php

namespace Framework\Facade;

use Framework\Foundation\Facade\Url;

function url_path(string $name): string
{
    return Url::static($name);
}

function url_for(string ...$args): string
{
    return Url::collect($args) ?? '';
}

function path_info(): string
{
    return Url::path_info();
}

function query_string(): string
{
    return Url::query_string();
}

class Kernel
{
    public function url_path(string $name): string
    {
        return Url::static($name);
    }

    public function url_for(string ...$args): null|string
    {
        return Url::collect($args);
    }

    public function path_info(): string
    {
        return Url::path_info();
    }

    public function query_string(): string
    {
        return Url::query_string();
    }
}
