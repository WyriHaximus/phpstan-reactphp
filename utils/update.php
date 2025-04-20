<?php

declare(strict_types=1);

use WyriHaximus\React\PHPStan\Utils\Func;
use WyriHaximus\React\PHPStan\Utils\ListFunctions;
use WyriHaximus\React\PHPStan\Utils\Readme;
use WyriHaximus\React\PHPStan\Utils\Rules\UseNonBlockingImplementationsRulePopulator;

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$funcs = [...ListFunctions::listAllBlockingFunctions()];
uasort($funcs, static fn (Func $left, Func $right): int => $left->name <=> $right->name);
Readme::update(...$funcs);
UseNonBlockingImplementationsRulePopulator::populate(...$funcs);
