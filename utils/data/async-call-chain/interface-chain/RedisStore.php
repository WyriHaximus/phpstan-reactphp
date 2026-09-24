<?php // phpcs:disable

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Data\AsyncCallChain\InterfaceChain;

use function React\Async\await;

interface DeclaresCount
{
    public function count(): int;
}

final class RedisStore implements DeclaresCount, Store
{
    public mixed $promise = null;

    public function label(): string
    {
        return 'redis';
    }

    public function snapshot(): int
    {
        return $this->count();
    }

    public function count(): int
    {
        return await($this->promise);
    }
}
