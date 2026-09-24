<?php // phpcs:disable

declare(strict_types=1);

use function React\Async\async;
use function React\Async\await;

$awaiting = static fn (): mixed => await($promise);

async($awaiting);

$asyncing = async(...);
$asyncing($awaiting);
