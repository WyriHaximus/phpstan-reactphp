<?php // phpcs:disable

declare(strict_types=1);

use React\EventLoop\Loop;

final class NotALoop
{
    public function futureTick(callable $listener): void
    {
    }

    public static function get(): self
    {
        return new self();
    }
}

$method = (string) getenv('LOOP_METHOD');
$class  = (string) getenv('LOOP_CLASS');

$loop = Loop::get();

$loop->$method(static fn (): null => null);
$loop->notALoopInterfaceMethod(static fn (): null => null);

Loop::get()->$method(static fn (): null => null);
Loop::get()->notALoopInterfaceMethod(static fn (): null => null);
Loop::$method()->futureTick(static fn (): null => null);
$class::get()->futureTick(static fn (): null => null);

NotALoop::get()->futureTick(static fn (): null => null);

$notALoop = new NotALoop();
$notALoop->futureTick(static fn (): null => null);
