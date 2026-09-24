<?php // phpcs:disable

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Utils\Data\Async;

function await(bool $ready): bool
{
    return $ready;
}

$awaiting = static fn (): bool => await(true);
