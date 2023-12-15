<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

use function array_key_exists;

/**
 * This rule checks that no event loop blocking functions are used in code.
 *
 * @implements Rule<Node\Expr\FuncCall>
 */
final readonly class UseNonBlockingImplementationsRule implements Rule
{
    // phpcs:disable
    private const FUNCTION_LIST = ['time_sleep_until' => 'time_sleep_until blocks the event loop, use React\\Promise\\Timer\\sleep from react/promise-timer instead. Please consult the documentation for more information: https://reactphp.org/promise-timer/#sleep', 'is_link' => 'is_link blocks the event loop, use React\\Filesystem\\AdapterInterface::detect from react/filesystem instead. Please consult the documentation for more information: https://github.com/reactphp/filesystem/?tab=readme-ov-file#detect', 'is_file' => 'is_file blocks the event loop, use React\\Filesystem\\AdapterInterface::detect from react/filesystem instead. Please consult the documentation for more information: https://github.com/reactphp/filesystem/?tab=readme-ov-file#detect', 'file_put_contents' => 'file_put_contents blocks the event loop, use React\\Filesystem\\Node\\FileInterface::putContents from react/filesystem instead. Please consult the documentation for more information: https://github.com/reactphp/filesystem/?tab=readme-ov-file#putcontents', 'is_dir' => 'is_dir blocks the event loop, use React\\Filesystem\\AdapterInterface::detect from react/filesystem instead. Please consult the documentation for more information: https://github.com/reactphp/filesystem/?tab=readme-ov-file#detect', 'file_exists' => 'file_exists blocks the event loop, use React\\Filesystem\\AdapterInterface::detect from react/filesystem instead. Please consult the documentation for more information: https://github.com/reactphp/filesystem/?tab=readme-ov-file#detect', 'sleep' => 'sleep blocks the event loop, use React\\Promise\\Timer\\sleep from react/promise-timer instead. Please consult the documentation for more information: https://reactphp.org/promise-timer/#sleep', 'mkdir' => 'mkdir blocks the event loop, use React\\Filesystem\\Node\\NotExistInterface::createDirectory from react/filesystem instead. Please consult the documentation for more information: https://github.com/reactphp/filesystem/?tab=readme-ov-file#createdirectory', 'time_nanosleep' => 'time_nanosleep blocks the event loop, use React\\Promise\\Timer\\sleep from react/promise-timer instead. Please consult the documentation for more information: https://reactphp.org/promise-timer/#sleep', 'usleep' => 'usleep blocks the event loop, use React\\Promise\\Timer\\sleep from react/promise-timer instead. Please consult the documentation for more information: https://reactphp.org/promise-timer/#sleep', 'file_get_contents' => 'file_get_contents blocks the event loop, use React\\Filesystem\\Node\\FileInterface::getContents from react/filesystem instead. Please consult the documentation for more information: https://github.com/reactphp/filesystem/?tab=readme-ov-file#getcontents'];
    // phpcs:enable
    public function getNodeType(): string
    {
        return Node\Expr\FuncCall::class;
    }

    /**
     * @param Node\Expr\FuncCall $node
     *
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Node\Name) {
            return [];
        }

        $functionName = $node->name->toString();
        if (array_key_exists($functionName, self::FUNCTION_LIST)) {
            return [self::FUNCTION_LIST[$functionName]];
        }

        return [];
    }
}
