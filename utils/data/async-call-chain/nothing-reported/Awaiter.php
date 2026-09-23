<?php // phpcs:disable

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Data\AsyncCallChain\NothingReported;

use function React\Async\async;
use function React\Async\await;

final class Awaiter
{
    public mixed $promise = null;

    public function fetch(): int
    {
        return await($this->promise);
    }

    public function wrappedAwait(): mixed
    {
        return async(fn (): mixed => await($this->promise));
    }

    public function wrappedCall(): mixed
    {
        return async(fn (): int => $this->fetch());
    }
}
