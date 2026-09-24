<?php

declare(strict_types=1);

use WyriHaximus\React\PHPStan\Utils\DocumentationQa;

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

DocumentationQa::run($argv[1] ?? '', ...array_slice($argv, 2));
