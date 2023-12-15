<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Utils;

use DirectoryIterator;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\ParserFactory;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;

use function current;
use function file_get_contents;
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

                $tokens     = new TokenIterator($lexer->tokenize(current($astNode->getComments())->getText()));
                $phpDocNode = $phpDocParser->parse($tokens);

                $function    = substr($filesystemNode->getFilename(), 0, -4);
                $url         = current($phpDocNode->getTagsByName('@url'))->value->value;
                $package     = current($phpDocNode->getTagsByName('@package'))->value->value;
                $replacement = current($phpDocNode->getTagsByName('@replacement'))->value->value;

                yield new Func(
                    $function,
                    $nodePath,
                    $package,
                    $replacement,
                    $url,
                    $function . ' blocks the event loop, use ' . $replacement . ' from ' . $package . ' instead. Please consult the documentation for more information: ' . $url,
                    $astNode->getLine(),
                );
            }
        }
    }
}
