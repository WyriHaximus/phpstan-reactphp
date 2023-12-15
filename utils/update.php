<?php

declare(strict_types=1);

use WyriHaximus\React\PHPStan\Utils\Rules\UseNonBlockingImplementationsRulePopulator;

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

UseNonBlockingImplementationsRulePopulator::populate();
