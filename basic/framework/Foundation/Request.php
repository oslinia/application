<?php

namespace Framework\Foundation;

use Framework\Foundation\Facade\{Render, Url};

function buffer(): string
{
    ob_start();

    extract(Render::$buffer['context']);

    require Render::$buffer['file'];

    return ob_get_clean();
}

class Request extends Render
{
    private function status(int $code): void
    {
        if (ob_get_level())
            ob_end_clean();

        header($_SERVER["SERVER_PROTOCOL"] . [
            200 => ' 200 OK',
            301 => ' 301 Moved Permanently',
            302 => ' 302 Moved Temporarily',
            307 => ' 307 Temporary Redirect',
            308 => ' 308 Permanent Redirect',
            404 => ' 404 Not Found',
            500 => ' 500 Internal Server Error',
        ][$code]);
    }

    private function content(int $length, string $mimetype, null|string $encoding): void
    {
        if (str_starts_with($mimetype, 'text/'))
            $mimetype .= '; charset=' . ($encoding ?? 'UTF-8');

        header('content-length: ' . $length);
        header('content-type: ' . $mimetype);
    }

    private function media(array $callback): void
    {
        [$filename, $encoding] = $callback;

        $mimetype = fn() => match (pathinfo($filename, PATHINFO_EXTENSION)) {
            'css' => 'text/css',
            'htm', 'html' => 'text/html',
            'txt' => 'text/plain',
            'xml' => 'text/xml',
            'gif' => 'image/gif',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'svg' => 'image/svg+xml',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'pdf' => 'application/pdf',
            default => 'application/octet-stream'
        };

        $this->status(200);
        $this->content(filesize($filename), $mimetype(), $encoding);

        if ($f = fopen($filename, 'rb')) {
            while (!feof($f))
                echo fread($f, 1024);

            fclose($f);
        }
    }

    private function document(array|string $callback): void
    {
        $extract = function (
            string      $body,
            int|null    $code = null,
            null|string $mimetype = null,
            null|string $encoding = null,
        ): void {
            $this->status($code ?? 200);
            $this->content(strlen($body), $mimetype ?? 'text/plain', $encoding);

            echo $body;
        };

        is_array($callback) ? $extract(...$callback) : $extract($callback);
    }

    public function __construct(string $dirname)
    {
        parent::init($dirname);

        $callback = parent::callback(Url::path_info());

        parent::$bool ? $this->media($callback) : $this->document($callback);
    }
}
