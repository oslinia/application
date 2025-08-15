<?php

namespace Framework\Foundation\Mapping;

use Framework\Facade\Path;

class Mapper
{
    protected static array $endpoint = array();
    protected static bool $bool;
    protected static array $tmp;

    private string $folder;

    private function caching(string $routing): void
    {
        mkdir($routing);

        $patterns = $masks = $map = array();

        foreach (self::$tmp as $path => $items) {
            [$name, $size] = $items;

            $pattern = '/^' . str_replace('/', '\/', $path) . '$/';

            $masks[$name][$size] = null;

            0 === $size || [$masks[$name][$size], $pattern] = [
                $items[2],
                str_replace(array_keys($items[3]), array_values($items[3]), $pattern),
            ];

            $patterns[$pattern] = $name;

            $map[$name][$size] = [$path, $pattern];
        }

        foreach (['patterns' => $patterns, 'masks' => $masks, 'map' => $map] as $name => $value) {
            $f = fopen($this->folder . $name . '.php', 'w');
            fwrite($f, '<?php return ' . var_export($value, true) . ';');
            fclose($f);
        }
    }

    protected function urls(string $routing, string $app): array
    {
        self::$bool = is_dir($routing);
        self::$bool || self::$tmp = [];

        require $app;

        $this->folder = $routing . DIRECTORY_SEPARATOR;

        self::$bool || $this->caching($routing);

        return require $this->folder . 'map.php';
    }

    protected function callback(string $path_info): array|string
    {
        foreach (require $this->folder . 'patterns.php' as $pattern => $name)
            if (preg_match($pattern, $path_info, $matches))
                if (isset(self::$endpoint[$name])) {
                    [$class, $method, $middleware] = self::$endpoint[$name];

                    $value = array_slice($matches, 1);

                    $masks = (require $this->folder . 'masks.php')[$name];

                    if (0 < $size = count($value)) {
                        if (isset($masks[$size])) {
                            $tokens = array();

                            foreach ($value as $i => $pattern)
                                $tokens[$masks[$size][$i]] = $pattern;

                            array_unshift($middleware, new Path($tokens));

                            return new $class()->$method(...$middleware);
                        }
                    } else {
                        return new $class()->$method(...$middleware);
                    }
                }

        return ['Not Found', 404, null, 'ASCII'];
    }
}
