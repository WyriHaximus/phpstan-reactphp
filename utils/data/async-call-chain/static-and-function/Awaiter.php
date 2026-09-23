<?php // phpcs:disable

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Data\AsyncCallChain\StaticAndFunction;

use function React\Async\await;

final class Awaiter
{
    public function __construct(public mixed $promise = null)
    {
        await($promise);
    }
}
