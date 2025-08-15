<?php

namespace Application;

use Framework\Facade\{Path, Response};

use function Framework\Facade\{url_for, url_path};

class Endpoint extends Response
{
    public function __invoke(): array
    {
        return [url_path('style.css') . PHP_EOL . url_for('archive', year: '2025'), null, null, 'ASCII'];
    }

    public function page(Path $path): string
    {
        return parent::url_path('style.css') . PHP_EOL . 'name: ' . $path->name;
    }

    public function media(Path $path): array
    {
        return parent::render_media($path->name);
    }

    public function template(Path $path): array
    {
        $context = [
            'lang' => 'ru',
            'content' => 'Hello, World!'
        ];

        return parent::render_template($path->name . '.php', $context);
    }

    public function archive(Path $path): array
    {
        parent::charset('ASCII');

        $body = 'Path year: ' . $path->year;

        if (isset($path->month))
            $body .= ' month: ' . $path->month;

        if (isset($path->day))
            $body .= ' day: ' . $path->day;

        $body .= PHP_EOL . 'path_info: ' . parent::path_info();
        $body .= PHP_EOL . 'query_string: \'' . parent::query_string() . '\'';

        return parent::response($body . PHP_EOL .
            parent::url_for('archive', year: '2025') . PHP_EOL .
            parent::url_for('archive', year: '2025', month: '05') . PHP_EOL .
            parent::url_for('archive', year: '2025', month: '05', day: '25') . PHP_EOL);
    }
}
