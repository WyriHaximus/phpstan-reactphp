<?php // phpcs:disable

declare(strict_types=1);

use WyriHaximus\React\PHPStan\Data\AsyncCallChain\StaticAndFunction\Loader;

use function React\Async\await;

function fetch(): int
{
    return await(Loader::$promise);
}
