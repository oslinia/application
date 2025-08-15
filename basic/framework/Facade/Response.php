<?php

namespace Framework\Facade;

use Framework\Foundation\Facade\Render;

use function Framework\Foundation\buffer;

class Response extends Kernel
{
    private null|string $encoding = null;

    public function charset(string $encoding): void
    {
        $this->encoding = $encoding;
    }

    public function render_media(string $name): array
    {
        $render = new Render('resource', 'media', $name);

        if ($render->media_exists())
            return [$render->filename, $this->encoding];

        return ['File not found', 404, null, 'ASCII'];
    }

    public function render_template(
        string      $name,
        array|null  $context = null,
        int|null    $code = null,
        null|string $mimetype = null,
    ): array {
        $render = new Render('src', 'templates', $name);

        if ($render->template_exists()) {
            $render->buffer_init($context);

            return [buffer(), $code, $mimetype ?? 'text/html', $this->encoding];
        }

        return ['Template not found', 500, null, 'ASCII'];
    }

    public function response(string $body, int|null $code = null, null|string $mimetype = null): array
    {
        return [$body, $code, $mimetype, $this->encoding];
    }
}
