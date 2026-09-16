<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function array_key_exists;

/**
 * This rule checks that no event loop blocking functions are used in code.
 *
 * @implements Rule<Node\Expr\FuncCall>
 */
final readonly class UseNonBlockingImplementationsRule implements Rule
{
    private const array FUNCTION_LIST = [
        'fclose' => [
            'name' => 'fclose',
            'identifier' => 'wyrihaximus.reactphp.blocking.function.fclose',
            'message' => 'fclose blocks the event loop, do not use it.',
            'tip' => 'Please consult the documentation for more information: ',
        ],
        'file_exists' => [
            'name' => 'file_exists',
            'identifier' => 'wyrihaximus.reactphp.blocking.function.fileExists',
            'message' => 'file_exists blocks the event loop, use React\Filesystem\AdapterInterface::detect from react/filesystem instead.',
            'tip' => 'Please consult the documentation for more information: https://github.com/reactphp/filesystem/?tab=readme-ov-file#detect',
        ],
        'file_get_contents' => [
            'name' => 'file_get_contents',
            'identifier' => 'wyrihaximus.reactphp.blocking.function.fileGetContents',
            'message' => 'file_get_contents blocks the event loop, use React\Filesystem\Node\FileInterface::getContents from react/filesystem instead.',
            'tip' => 'Please consult the documentation for more information: https://github.com/reactphp/filesystem/?tab=readme-ov-file#getcontents',
        ],
        'file_put_contents' => [
            'name' => 'file_put_contents',
            'identifier' => 'wyrihaximus.reactphp.blocking.function.filePutContents',
            'message' => 'file_put_contents blocks the event loop, use React\Filesystem\Node\FileInterface::putContents from react/filesystem instead.',
            'tip' => 'Please consult the documentation for more information: https://github.com/reactphp/filesystem/?tab=readme-ov-file#putcontents',
        ],
        'fopen' => [
            'name' => 'fopen',
            'identifier' => 'wyrihaximus.reactphp.blocking.function.fopen',
            'message' => 'fopen blocks the event loop, use React\Filesystem\Node\FileInterface::getContents, React\Socket\Connector::connect from react/filesystem, react/socket instead.',
            'tip' => 'Please consult the documentation for more information: https://github.com/reactphp/filesystem/?tab=readme-ov-file#getcontents, https://reactphp.org/socket/#connect',
        ],
        'fread' => [
            'name' => 'fread',
            'identifier' => 'wyrihaximus.reactphp.blocking.function.fread',
            'message' => 'fread blocks the event loop, use React\Stream\ReadableStreamInterface::on from react/stream instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/stream/#data-event',
        ],
        'fwrite' => [
            'name' => 'fwrite',
            'identifier' => 'wyrihaximus.reactphp.blocking.function.fwrite',
            'message' => 'fwrite blocks the event loop, use React\Stream\WritableStreamInterface::write from react/stream instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/stream/#write',
        ],
        'gethostbyname' => [
            'name' => 'gethostbyname',
            'identifier' => 'wyrihaximus.reactphp.blocking.function.gethostbyname',
            'message' => 'gethostbyname blocks the event loop, use React\Dns\ResolverInterface::resolve from react/dns instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/dns/#resolve',
        ],
        'is_dir' => [
            'name' => 'is_dir',
            'identifier' => 'wyrihaximus.reactphp.blocking.function.isDir',
            'message' => 'is_dir blocks the event loop, use React\Filesystem\AdapterInterface::detect from react/filesystem instead.',
            'tip' => 'Please consult the documentation for more information: https://github.com/reactphp/filesystem/?tab=readme-ov-file#detect',
        ],
        'is_file' => [
            'name' => 'is_file',
            'identifier' => 'wyrihaximus.reactphp.blocking.function.isFile',
            'message' => 'is_file blocks the event loop, use React\Filesystem\AdapterInterface::detect from react/filesystem instead.',
            'tip' => 'Please consult the documentation for more information: https://github.com/reactphp/filesystem/?tab=readme-ov-file#detect',
        ],
        'is_link' => [
            'name' => 'is_link',
            'identifier' => 'wyrihaximus.reactphp.blocking.function.isLink',
            'message' => 'is_link blocks the event loop, use React\Filesystem\AdapterInterface::detect from react/filesystem instead.',
            'tip' => 'Please consult the documentation for more information: https://github.com/reactphp/filesystem/?tab=readme-ov-file#detect',
        ],
        'mkdir' => [
            'name' => 'mkdir',
            'identifier' => 'wyrihaximus.reactphp.blocking.function.mkdir',
            'message' => 'mkdir blocks the event loop, use React\Filesystem\Node\NotExistInterface::createDirectory from react/filesystem instead.',
            'tip' => 'Please consult the documentation for more information: https://github.com/reactphp/filesystem/?tab=readme-ov-file#createdirectory',
        ],
        'sleep' => [
            'name' => 'sleep',
            'identifier' => 'wyrihaximus.reactphp.blocking.function.sleep',
            'message' => 'sleep blocks the event loop, use React\Promise\Timer\sleep from react/promise-timer instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/promise-timer/#sleep',
        ],
        'time_nanosleep' => [
            'name' => 'time_nanosleep',
            'identifier' => 'wyrihaximus.reactphp.blocking.function.timeNanosleep',
            'message' => 'time_nanosleep blocks the event loop, use React\Promise\Timer\sleep from react/promise-timer instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/promise-timer/#sleep',
        ],
        'time_sleep_until' => [
            'name' => 'time_sleep_until',
            'identifier' => 'wyrihaximus.reactphp.blocking.function.timeSleepUntil',
            'message' => 'time_sleep_until blocks the event loop, use React\Promise\Timer\sleep from react/promise-timer instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/promise-timer/#sleep',
        ],
        'usleep' => [
            'name' => 'usleep',
            'identifier' => 'wyrihaximus.reactphp.blocking.function.usleep',
            'message' => 'usleep blocks the event loop, use React\Promise\Timer\sleep from react/promise-timer instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/promise-timer/#sleep',
        ],
    ];

    public function getNodeType(): string
    {
        return Node\Expr\FuncCall::class;
    }

    /** @inheritDoc */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Node\Name) {
            return [];
        }

        $functionName = $node->name->toString();
        if (array_key_exists($functionName, self::FUNCTION_LIST)) {
            return [RuleErrorBuilder::message(self::FUNCTION_LIST[$functionName]['message'])->identifier(self::FUNCTION_LIST[$functionName]['identifier'])->build()];
        }

        return [];
    }
}
