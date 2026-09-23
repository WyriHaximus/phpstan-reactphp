<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use WyriHaximus\React\PHPStan\Collectors\AsyncCallGraphCollector;

use function array_key_exists;
use function array_shift;
use function sprintf;

/**
 * This checks that closures calling into code that awaits are wrapped in async where they are defined.
 *
 * Where WrapAwaitingClosuresInAsyncRule looks at the closure itself, this one follows the calls made in that closure
 * through the rest of the analysed code until it finds an await.
 *
 * @implements Rule<CollectedDataNode>
 */
final readonly class WrapClosuresReachingAwaitInAsyncRule implements Rule
{
    public const string MESSAGE = '%s awaits further down the call stack, wrap the closure it is called in with React\Async\async from react/async.';
    public const string TIP     = 'React\Async\await is called in %s at %s:%d. Please consult the documentation for more information: https://reactphp.org/async/#async';

    private const string IDENTIFIER = 'wyrihaximus.reactphp.async.awaitReachedWithoutAsync';

    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }

    /** @inheritDoc */
    public function processNode(Node $node, Scope $scope): array
    {
        /** @var array<string, array{name: string, file: string, line: int}> $awaiting */
        $awaiting = [];
        $callees  = [];
        /** @var list<array{callees: list<string>, name: string, file: string, line: int}> $roots */
        $roots = [];
        foreach ($node->get(AsyncCallGraphCollector::class) as $collected) {
            foreach ($collected as $record) {
                if ($record['type'] === 'await') {
                    foreach ($record['keys'] as $key) {
                        $awaiting[$key] = [
                            'name' => $record['name'],
                            'file' => $record['file'],
                            'line' => $record['line'],
                        ];
                    }
                } elseif ($record['type'] === 'root') {
                    $roots[] = [
                        'callees' => $record['callees'],
                        'name' => $record['name'],
                        'file' => $record['file'],
                        'line' => $record['line'],
                    ];
                } else {
                    foreach ($record['callers'] as $caller) {
                        foreach ($record['callees'] as $callee) {
                            $callees[$caller][] = $callee;
                        }
                    }
                }
            }
        }

        $errors = [];
        foreach ($roots as $root) {
            $await = self::reachableAwait($root['callees'], $awaiting, $callees);
            if ($await === null) {
                continue;
            }

            $errors[] = RuleErrorBuilder::message(sprintf(self::MESSAGE, $root['name']))
            ->file($root['file'])
            ->line($root['line'])
            ->identifier(self::IDENTIFIER)
            ->tip(sprintf(self::TIP, $await['name'], $await['file'], $await['line']))
            ->build();
        }

        return $errors;
    }

    /**
     * @param list<string>                                                $keys
     * @param array<string, array{name: string, file: string, line: int}> $awaiting
     * @param array<string, list<string>>                                 $callees
     *
     * @return array{name: string, file: string, line: int}|null
     */
    private static function reachableAwait(array $keys, array $awaiting, array $callees): array|null
    {
        while ($keys !== []) {
            $key = array_shift($keys);
            if (array_key_exists($key, $awaiting)) {
                return $awaiting[$key];
            }

            foreach ($callees[$key] ?? [] as $callee) {
                $keys[] = $callee;
            }

            unset($callees[$key]);
        }

        return null;
    }
}
