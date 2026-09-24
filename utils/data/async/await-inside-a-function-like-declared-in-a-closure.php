<?php // phpcs:disable

declare(strict_types=1);

use React\Promise\PromiseInterface;

use function React\Promise\resolve;
use function React\Async\await;

$promise = resolve(true);

$declaringAFunction = static function () use ($promise): bool {
    function awaitInAFunctionDeclaredInAClosure(PromiseInterface $promise): bool
    {
        return await($promise);
    }

    return awaitInAFunctionDeclaredInAClosure($promise);
};

$declaringAClass = static function () use ($promise): object {
    return new class ($promise) {
        public function __construct(private PromiseInterface $promise)
        {
        }

        public function awaitIt(): bool
        {
            return await($this->promise);
        }
    };
};
