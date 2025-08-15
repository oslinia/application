<?php

use Framework\Foundation\Request;

$dirname = dirname(__DIR__);

require __DIR__ . '/../resource/autoload.php';

new Request($dirname);
