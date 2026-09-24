<?php // phpcs:disable

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Data\AsyncCallChain\NothingReported;

final class Caller
{
    public Awaiter $awaiter;

    public Recursive $recursive;

    public mixed $anything = null;

    public function run(callable $tick, string $method, string $class): void
    {
        $tick(function () use ($method, $class): void {
            $this->recursive->ping();
            $this->awaiter->wrappedAwait();
            $this->awaiter->wrappedCall();
            $this->awaiter->fetch(...);
            $this->awaiter->$method();
            $this->anything->fetch();
            new $class();
        });
    }
}
