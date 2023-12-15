<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Utils\Rules;

use PhpParser\Node\Const_;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use WyriHaximus\React\PHPStan\Utils\Func;
use WyriHaximus\React\PHPStan\Utils\ListFunctions;

use function dirname;
use function file_get_contents;
use function Safe\file_put_contents;

use const DIRECTORY_SEPARATOR;

final readonly class UseNonBlockingImplementationsRulePopulator
{
    public static function populate(): void
    {
        $functionsRuleFile = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Rules' . DIRECTORY_SEPARATOR . 'UseNonBlockingImplementationsRule.php';
        $parser            = (new ParserFactory())->createForNewestSupportedVersion();
        $ast               = $parser->parse(file_get_contents($functionsRuleFile));
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
                                new Name('FUNCTION_LIST'),
                                new Array_([
                                    ...(static function (Func ...$functions): iterable {
                                        foreach ($functions as $function) {
                                            yield new ArrayItem(
                                                new String_($function->error),
                                                new String_($function->name),
                                            );
                                        }
                                    })(...ListFunctions::listAllBlockingFunctions()),
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

        file_put_contents($functionsRuleFile, (new Standard())->prettyPrintFile($ast));
    }
}
