<?php // phpcs:disable

declare(strict_types=1);

$function = (string) getenv('FUNCTION');

$function('php://memory', 'r');

strlen('this one doesn\'t block');
