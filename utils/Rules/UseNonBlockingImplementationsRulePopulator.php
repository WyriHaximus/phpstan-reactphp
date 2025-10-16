<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Utils\Rules;

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
use WyriHaximus\React\PHPStan\Utils\Func;

use function dirname;
use function file_get_contents;
use function implode;
use function is_string;
use function Safe\file_put_contents;
use function str_replace;

use const DIRECTORY_SEPARATOR;
use const PHP_EOL;

final readonly class UseNonBlockingImplementationsRulePopulator
{
    public static function populate(Func ...$funcs): void
    {
        $functionsRuleFile         = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Rules' . DIRECTORY_SEPARATOR . 'UseNonBlockingImplementationsRule.php';
        $parser                    = new ParserFactory()->createForNewestSupportedVersion();
        $functionsRuleFileContents = file_get_contents($functionsRuleFile); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.fileGetContents */
        if (! is_string($functionsRuleFileContents)) {
            throw new RuntimeException('Unable to read functions rule file: ' . $functionsRuleFile);
        }

        $ast = $parser->parse($functionsRuleFileContents);
        if ($ast === null) {
            throw new RuntimeException('Unable to parse functions rule file: ' . $functionsRuleFile);
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
                        if ($const->name->toString() === 'FUNCTION_LIST') {
                            $const = new Const_(
                                'FUNCTION_LIST',
                                new Array_([
                                    ...self::functionArrayItemBuilder(...$funcs),
                                ], [
                                    'kind' => Array_::KIND_SHORT,
                                ]),
                            );
                        }

                        $subStmt->consts[$k] = $const;
                    }
                }
            }
        }

        file_put_contents($functionsRuleFile, self::postProcessing(new Standard()->prettyPrintFile($ast)));
    }

    /** @return iterable<ArrayItem> */
    public static function functionArrayItemBuilder(Func ...$functions): iterable
    {
        foreach ($functions as $function) {
            yield new ArrayItem(
                new Array_([
                    new ArrayItem(
                        new String_($function->name),
                        new String_('name'),
                    ),
                    new ArrayItem(
                        new String_('wyrihaximus.reactphp.blocking.function.' . new Convert($function->name)->toCamel()),
                        new String_('identifier'),
                    ),
                    new ArrayItem(
                        new String_($function->error),
                        new String_('message'),
                    ),
                    new ArrayItem(
                        new String_('Please consult the documentation for more information: ' . implode(', ', $function->url)),
                        new String_('tip'),
                    ),
                ], [
                    'kind' => Array_::KIND_SHORT,
                ]),
                new String_($function->name),
            );
        }
    }

    private static function postProcessing(string $php): string
    {
        $php = str_replace('private const FUNCTION_LIST = [\'', 'private const FUNCTION_LIST = [' . PHP_EOL . '\'', $php);
        $php = str_replace('\']', '\'' . PHP_EOL . ']', $php);
        $php = str_replace('\', \'', '\',' . PHP_EOL . '\'', $php);

        return $php;
    }
}
