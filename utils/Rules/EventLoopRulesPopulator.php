<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Utils\Rules;

use Closure;
use Jawira\CaseConverter\Convert;
use PhpParser\Node\Const_;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use RuntimeException;
use WyriHaximus\React\PHPStan\Utils\LoopMethod;

use function dirname;
use function file_get_contents;
use function file_put_contents;
use function is_string;
use function str_replace;

use const DIRECTORY_SEPARATOR;
use const PHP_EOL;

final readonly class EventLoopRulesPopulator
{
    private const string CONSTANT = 'METHOD_LIST';

    public static function populate(LoopMethod ...$methods): void
    {
        self::populateRule(
            'UseLoopStaticProxiesRule',
            'wyrihaximus.reactphp.eventLoop.staticProxy.',
            static fn (LoopMethod $method): string => $method->staticProxyError,
            ...$methods,
        );

        self::populateRule(
            'DoNotUseLoopInstancesRule',
            'wyrihaximus.reactphp.eventLoop.instance.',
            static fn (LoopMethod $method): string => $method->instanceError,
            ...$methods,
        );
    }

    /** @param Closure(LoopMethod): string $message */
    private static function populateRule(string $rule, string $identifierPrefix, Closure $message, LoopMethod ...$methods): void
    {
        $ruleFile         = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Rules' . DIRECTORY_SEPARATOR . $rule . '.php';
        $parser           = new ParserFactory()->createForNewestSupportedVersion();
        $ruleFileContents = file_get_contents($ruleFile); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.fileGetContents */
        if (! is_string($ruleFileContents)) {
            throw new RuntimeException('Unable to read rule file: ' . $ruleFile);
        }

        $ast = $parser->parse($ruleFileContents);
        if ($ast === null) {
            throw new RuntimeException('Unable to parse rule file: ' . $ruleFile);
        }

        foreach ($ast as $node) {
            if (! ($node instanceof Namespace_)) {
                continue;
            }

            foreach ($node->stmts as $stmt) {
                if (! ($stmt instanceof Class_)) {
                    continue;
                }

                foreach ($stmt->stmts as $subStmt) {
                    if (! ($subStmt instanceof ClassConst)) {
                        continue;
                    }

                    foreach ($subStmt->consts as $k => $const) {
                        if ($const->name->toString() !== self::CONSTANT) {
                            continue;
                        }

                        $subStmt->consts[$k] = new Const_(
                            self::CONSTANT,
                            new Array_([
                                ...self::methodArrayItemBuilder($identifierPrefix, $message, ...$methods),
                            ], ['kind' => Array_::KIND_SHORT]),
                        );
                    }
                }
            }
        }

        file_put_contents($ruleFile, self::postProcessing(new Standard()->prettyPrintFile($ast))); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.filePutContents */
    }

    /**
     * @param Closure(LoopMethod): string $message
     *
     * @return iterable<ArrayItem>
     */
    private static function methodArrayItemBuilder(string $identifierPrefix, Closure $message, LoopMethod ...$methods): iterable
    {
        foreach ($methods as $method) {
            yield new ArrayItem(
                new Array_([
                    new ArrayItem(
                        new String_($method->name),
                        new String_('name'),
                    ),
                    new ArrayItem(
                        new String_($identifierPrefix . new Convert($method->name)->toCamel()),
                        new String_('identifier'),
                    ),
                    new ArrayItem(
                        new String_($message($method)),
                        new String_('message'),
                    ),
                    new ArrayItem(
                        new String_($method->tip),
                        new String_('tip'),
                    ),
                ], ['kind' => Array_::KIND_SHORT]),
                new String_($method->name),
            );
        }
    }

    private static function postProcessing(string $php): string
    {
        $php = str_replace('private const array ' . self::CONSTANT . ' = [\'', 'private const array ' . self::CONSTANT . ' = [' . PHP_EOL . '\'', $php);
        $php = str_replace('\']', '\'' . PHP_EOL . ']', $php);
        $php = str_replace('\', \'', '\',' . PHP_EOL . '\'', $php);

        return $php;
    }
}
