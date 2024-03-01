<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Utils;

use DirectoryIterator;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\ParserFactory;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;

use function count;
use function current;
use function file_get_contents;
use function implode;
use function is_array;
use function is_file;
use function property_exists;
use function substr;

use const DIRECTORY_SEPARATOR;

final class ListFunctions
{
    /** @return iterable<Func> */
    public static function listAllBlockingFunctions(): iterable
    {
        $lexer           = new Lexer();
        $constExprParser = new ConstExprParser();
        $typeParser      = new TypeParser($constExprParser);
        $phpDocParser    = new PhpDocParser($typeParser, $constExprParser);
        $parser          = (new ParserFactory())->createForNewestSupportedVersion();
        $root            = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR;
        foreach (new DirectoryIterator($root) as $filesystemNode) {
            $nodePath = $root . $filesystemNode->getFilename();
            if (! is_file($nodePath)) {
                continue;
            }

            $ast = $parser->parse(file_get_contents($nodePath));
            foreach ($ast as $astNode) {
                if (! property_exists($astNode, 'expr') || ! ($astNode->expr instanceof FuncCall)) {
                    continue;
                }

                $function = substr($filesystemNode->getFilename(), 0, -4);
                if (is_array($astNode->getComments()) && count($astNode->getComments()) > 0) {
                    $tokens     = new TokenIterator($lexer->tokenize(current($astNode->getComments())->getText()));
                    $phpDocNode = $phpDocParser->parse($tokens);

                    $url         = [...self::getValueValuesFromTag(...$phpDocNode->getTagsByName('@url'))];
                    $package     = [...self::getValueValuesFromTag(...$phpDocNode->getTagsByName('@package'))];
                    $replacement = [...self::getValueValuesFromTag(...$phpDocNode->getTagsByName('@replacement'))];

                    yield new Func(
                        $function,
                        $nodePath,
                        $package,
                        $replacement,
                        $url,
                        $function . ' blocks the event loop, use ' . implode(', ', $replacement) . ' from ' . implode(', ', $package) . ' instead. Please consult the documentation for more information: ' . implode(', ', $url),
                        $astNode->getLine(),
                    );

                    continue;
                }

                yield new Func(
                    $function,
                    $nodePath,
                    [],
                    [],
                    [],
                    $function . ' blocks the event loop, do not use it.',
                    $astNode->getLine(),
                );
            }
        }
    }

    /** @return iterable<string> */
    private static function getValueValuesFromTag(PhpDocTagNode ...$tags): iterable
    {
        foreach ($tags as $tag) {
            yield $tag->value->value;
        }
    }
}
