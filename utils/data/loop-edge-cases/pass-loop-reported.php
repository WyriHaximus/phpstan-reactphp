<?php // phpcs:disable

declare(strict_types=1);

use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;

function acceptLoop(LoopInterface $loop): void
{
}

function acceptLoopAfterOther(object $first, LoopInterface $second): void
{
}

function acceptTwoLoops(LoopInterface $first, LoopInterface $second): void
{
}

final class NotALoopForPass
{
}

acceptLoop(Loop::get());

$loop = Loop::get();

acceptLoop($loop);

final class LoopConsumer
{
    public function __construct(LoopInterface $loop)
    {
    }
}

new LoopConsumer(Loop::get());

final class StaticLoopAccept
{
    public static function go(LoopInterface $loop): void
    {
    }
}

StaticLoopAccept::go($loop);

final class InstanceLoopAccept
{
    public function go(LoopInterface $loop): void
    {
    }
}

(new InstanceLoopAccept())->go($loop);

acceptLoopAfterOther(new NotALoopForPass(), $loop);

acceptTwoLoops($loop, Loop::get());
