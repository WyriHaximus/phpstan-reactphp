<?php // phpcs:disable

declare(strict_types=1);

/**
 * @package react/filesystem
 * @replacement React\Filesystem\Node\FileInterface::putContents
 * @url https://github.com/reactphp/filesystem/?tab=readme-ov-file#putcontents
 */
file_put_contents(__FILE__, 'nope');
