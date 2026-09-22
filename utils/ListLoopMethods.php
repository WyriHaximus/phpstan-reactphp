<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Utils;

use DirectoryIterator;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use PHPStan\PhpDocParser\ParserConfig;
use RuntimeException;

use function count;
use function current;
use function file_get_contents;
use function implode;
use function is_file;
use function is_string;

use const DIRECTORY_SEPARATOR;

final class ListLoopMethods
{
    /** @return iterable<LoopMethod> */
    public static function listAllLoopMethods(): iterable
    {
        $config          = new ParserConfig(usedAttributes: []);
        $lexer           = new Lexer($config);
        $constExprParser = new ConstExprParser($config);
        $typeParser      = new TypeParser($config, $constExprParser);
        $phpDocParser    = new PhpDocParser($config, $typeParser, $constExprParser);
        $parser          = new ParserFactory()->createForNewestSupportedVersion();
        $root            = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'loop' . DIRECTORY_SEPARATOR;
        foreach (new DirectoryIterator($root) as $filesystemNode) {
            $nodePath = $root . $filesystemNode->getFilename();
            if (! is_file($nodePath)) {/** @phpstan-ignore wyrihaximus.reactphp.blocking.function.isFile */
                continue;
            }

            yield self::fromFile($nodePath, $parser, $phpDocParser, $lexer);
        }
    }

    private static function fromFile(string $path, Parser $parser, PhpDocParser $phpDocParser, Lexer $lexer): LoopMethod
    {
        $contents = file_get_contents($path); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.fileGetContents */
        if (! is_string($contents)) {
            throw new RuntimeException('Unable to read: ' . $path);
        }

        $ast = $parser->parse($contents);
        if ($ast === null) {
            throw new RuntimeException('Unable to parse: ' . $path);
        }

        $name            = null;
        $package         = [];
        $replacement     = [];
        $url             = [];
        $staticProxyLine = null;
        $instanceLine    = null;

        foreach ($ast as $astNode) {
            if (! ($astNode instanceof Expression) || ! ($astNode->expr instanceof MethodCall)) {
                continue;
            }

            $methodCall = $astNode->expr;
            $comments   = $astNode->getComments();
            if (! ($methodCall->name instanceof Identifier) || count($comments) === 0) {
                continue;
            }

            $name = $methodCall->name->toString();

            $tokens      = new TokenIterator($lexer->tokenize(current($comments)->getText()));
            $phpDocNode  = $phpDocParser->parse($tokens);
            $url         = [...self::getValueValuesFromTag(...$phpDocNode->getTagsByName('@url'))];
            $package     = [...self::getValueValuesFromTag(...$phpDocNode->getTagsByName('@package'))];
            $replacement = [...self::getValueValuesFromTag(...$phpDocNode->getTagsByName('@replacement'))];

            if ($methodCall->var instanceof StaticCall) {
                $staticProxyLine = $astNode->getStartLine();

                continue;
            }

            $instanceLine = $astNode->getStartLine();
        }

        if ($name === null || $staticProxyLine === null || $instanceLine === null) {
            throw new RuntimeException('Expected both a Loop::get() and a loop instance call in: ' . $path);
        }

        $suggestion = ', use ' . implode(', ', $replacement) . ' from ' . implode(', ', $package) . ' instead.';

        return new LoopMethod(
            $name,
            $path,
            'Loop::get()->' . $name . '() goes through the loop instance' . $suggestion,
            $staticProxyLine,
            'Calling ' . $name . ' on a loop instance is prohibited' . $suggestion,
            $instanceLine,
            'Please consult the documentation for more information: ' . implode(', ', $url),
        );
    }

    /** @return iterable<string> */
    private static function getValueValuesFromTag(PhpDocTagNode ...$tags): iterable
    {
        foreach ($tags as $tag) {
            /** @phpstan-ignore-next-line */
            yield $tag->value->value;
        }
    }
}
