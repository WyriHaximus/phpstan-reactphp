<?php // phpcs:disable

declare(strict_types=1);

use React\Promise\PromiseInterface;

use function React\Async\await;
use function React\Promise\resolve;

await(resolve(true));

function awaitInAFunction(PromiseInterface $promise): bool
{
    return await($promise);
}

final class AwaitInAMethod
{
    public function awaitIt(PromiseInterface $promise): bool
    {
        return await($promise);
    }
}
