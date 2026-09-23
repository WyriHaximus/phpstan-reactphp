<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\EventLoop;

use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

final readonly class LoopInterfaceType
{
    private const string LOOP_INTERFACE = 'React\EventLoop\LoopInterface';

    public static function accepts(Type $type): bool
    {
        return (new ObjectType(self::LOOP_INTERFACE))->isSuperTypeOf($type)->yes();
    }
}
