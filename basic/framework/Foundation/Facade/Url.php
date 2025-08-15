<?php

namespace Framework\Foundation\Facade;

class Url
{
    private static string $static;
    private static array $urls;

    private static array $request;

    public function __construct(string $static, array $urls)
    {
        self::$static = $static;
        self::$urls = $urls;

        self::$request = explode('?', $_SERVER['REQUEST_URI'], 2);
    }

    public static function static(string $name): string
    {
        return self::$static . $name;
    }

    public static function collect(array $args): null|string
    {
        $name = array_shift($args);

        if (isset(self::$urls[$name])) {
            $link = self::$urls[$name];

            $size = count($args);

            if (isset($link[$size])) {
                [$path, $pattern] = $link[$size];

                foreach ($args as $mask => $value)
                    $path = str_replace('{' . $mask . '}', $value, $path);

                if (preg_match($pattern, $path, $matches))
                    return $matches[0];
            }
        }

        return null;
    }

    public static function path_info(): string
    {
        return self::$request[0];
    }

    public static function query_string(): string
    {
        return self::$request[1] ?? '';
    }
}
