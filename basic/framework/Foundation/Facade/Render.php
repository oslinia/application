<?php

namespace Framework\Foundation\Facade;

use Framework\Foundation\Mapping\Mapper;

class Render extends Mapper
{
    protected static bool $bool = false;

    private static string $root;

    public string $filename;

    public static array $buffer;

    private static function root(string ...$args): string
    {
        return self::$root . implode(DIRECTORY_SEPARATOR, $args);
    }

    protected function init(string $dirname): void
    {
        self::$root = $dirname . DIRECTORY_SEPARATOR;

        $data = require self::root('resource', 'data.php');

        new Url(
            $data['static'],
            parent::urls(
                self::root('resource', 'mapping'),
                self::root('src', 'app', 'urls.php'),
            ),
        );
    }

    public function __construct(string ...$args)
    {
        $this->filename = self::root(...$args);
    }

    public function media_exists(): bool
    {
        return self::$bool = is_file($this->filename);
    }

    public function template_exists(): bool
    {
        return is_file($this->filename);
    }

    public function buffer_init(array|null $context): void
    {
        self::$buffer = [
            'file' => $this->filename,
            'context' => $context ?? [],
        ];
    }
}
