<?php // phpcs:disable

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Data\AsyncCallChain\StaticAndFunction;

use function React\Async\await;

final class Loader
{
    public static mixed $promise = null;

    public static function load(): int
    {
        return await(self::$promise);
    }
}
