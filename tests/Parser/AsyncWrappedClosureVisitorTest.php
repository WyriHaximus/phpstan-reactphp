<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\React\PHPStan\Parser;

use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use WyriHaximus\React\PHPStan\Parser\AsyncWrappedClosureVisitor;
use WyriHaximus\TestUtilities\TestCase;

final class AsyncWrappedClosureVisitorTest extends TestCase
{
    private const string CODE = <<<'PHP'
    <?php

    use function React\Async\async;
    use function React\Async\await;

    await($promise);
    async(static fn (): mixed => await($promise));
    (static fn (): mixed => await($promise))();
    function awaitInAFunction(mixed $promise): mixed
    {
        return await($promise);
    }

    async(...);
    async($notAClosure);
    notAsync(static fn (): mixed => await($promise));
    async($notAClosure, static fn (): mixed => await($promise));
    async(static function (): mixed {
        (static fn (): mixed => await($promise))();

        return await($promise);
    });
    PHP;

    public function testEnclosingClosureIsStampedOnEveryCall(): void
    {
        $parser    = new ParserFactory()->createForNewestSupportedVersion();
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor(new AsyncWrappedClosureVisitor());

        // The visitor gets reused for every file PHPStan parses, hence traversing more than once.
        for ($iteration = 0; $iteration < 2; $iteration++) {
            $ast = $parser->parse(self::CODE);
            self::assertNotNull($ast);

            $traverser->traverse($ast);

            self::assertSame(
                [
                    6 => null,
                    7 => true,
                    8 => false,
                    11 => null,
                    16 => false,
                    17 => true,
                    19 => false,
                    21 => true,
                ],
                self::enclosingClosures(...$ast),
            );
        }
    }

    /** @return array<int, mixed> */
    private static function enclosingClosures(Node\Stmt ...$ast): array
    {
        $enclosingClosures = [];
        foreach (new NodeFinder()->findInstanceOf($ast, Node\Expr\FuncCall::class) as $funcCall) {
            if (! ($funcCall->name instanceof Node\Name) || $funcCall->name->toLowerString() !== 'react\async\await') {
                continue;
            }

            $enclosingClosures[$funcCall->getStartLine()] = $funcCall->getAttribute(
                AsyncWrappedClosureVisitor::ATTRIBUTE_ENCLOSING_CLOSURE,
            );
        }

        return $enclosingClosures;
    }
}
