<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Utils;

use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;

use function array_filter;
use function array_values;
use function chmod;
use function copy;
use function dirname;
use function explode;
use function fclose;
use function file_get_contents;
use function file_put_contents;
use function filesize;
use function implode;
use function is_dir;
use function is_executable;
use function is_file;
use function is_int;
use function is_resource;
use function is_string;
use function mkdir;
use function php_uname;
use function proc_close;
use function proc_open;
use function realpath;
use function sort;
use function sprintf;
use function str_starts_with;
use function stream_get_contents;
use function strlen;
use function substr;
use function trim;
use function unlink;

use const DIRECTORY_SEPARATOR;

final class DocumentationQa
{
    private const string FILES_LIST_RELATIVE          = 'var/ci/documentation-files.lst';
    private const string LINKSPECTOR_CONFIG_RELATIVE  = 'var/ci/linkspector.yml';
    private const string LINKSPECTOR_BINARY_RELATIVE  = 'var/linkspector/linkspector';
    private const string LINKSPECTOR_OPTIONS_RELATIVE = 'etc/ci/linkspector.options.yml';
    private const string VALE_VOCAB_SRC_RELATIVE      = 'etc/qa/vale/vocabularies';
    private const string VALE_VOCAB_DEST_RELATIVE     = 'var/vale/styles/config/vocabularies';

    public static function run(string $command, string ...$arguments): void
    {
        match ($command) {
            'files-cache' => self::writeDocumentationFilesList(),
            'vale-vocab' => self::installValeVocab(),
            'linkspector-config' => self::prepareLinkspectorConfig(),
            'linkspector-binary' => self::ensureLinkspectorBinary($arguments[0] ?? 'v0.5.6'),
            default => throw new InvalidArgumentException(sprintf('Unknown documentation-qa command: %s', $command)),
        };
    }

    public static function writeDocumentationFilesList(): void
    {
        self::writeDocumentationFilesListIn(self::projectRoot());
    }

    public static function writeDocumentationFilesListIn(string $root): void
    {
        $paths = self::discoverDocumentationFiles($root);
        $list  = self::FILES_LIST_RELATIVE;
        self::ensureParentDirectory($root . DIRECTORY_SEPARATOR . $list);
        file_put_contents($root . DIRECTORY_SEPARATOR . $list, implode("\0", $paths) . ($paths === [] ? '' : "\0")); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.filePutContents */
    }

    public static function installValeVocab(): void
    {
        self::installValeVocabIn(self::projectRoot());
    }

    public static function installValeVocabIn(string $root): void
    {
        self::recursiveCopy(
            $root . DIRECTORY_SEPARATOR . self::VALE_VOCAB_SRC_RELATIVE,
            $root . DIRECTORY_SEPARATOR . self::VALE_VOCAB_DEST_RELATIVE,
        );
    }

    public static function prepareLinkspectorConfig(): void
    {
        self::prepareLinkspectorConfigIn(self::projectRoot());
    }

    public static function prepareLinkspectorConfigIn(string $root): void
    {
        $listPath = $root . DIRECTORY_SEPARATOR . self::FILES_LIST_RELATIVE;
        if (! is_file($listPath)) { /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.isFile */
            throw new RuntimeException('Missing documentation file list; run files-cache first.');
        }

        $raw = file_get_contents($listPath); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.fileGetContents */
        if (! is_string($raw)) {
            throw new RuntimeException('Unable to read documentation file list.');
        }

        $optionsPath = $root . DIRECTORY_SEPARATOR . self::LINKSPECTOR_OPTIONS_RELATIVE;
        $options     = file_get_contents($optionsPath); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.fileGetContents */
        if (! is_string($options)) {
            throw new RuntimeException('Unable to read linkspector options.');
        }

        $lines = ['files:'];
        foreach (self::splitNullSeparated($raw) as $path) {
            $lines[] = '  - ' . $path;
        }

        $out = $root . DIRECTORY_SEPARATOR . self::LINKSPECTOR_CONFIG_RELATIVE;
        self::ensureParentDirectory($out);
        file_put_contents($out, implode("\n", $lines) . "\n" . trim($options) . "\n"); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.filePutContents */
    }

    public static function ensureLinkspectorBinary(string $version): void
    {
        self::ensureLinkspectorBinaryIn($version, self::projectRoot());
    }

    public static function ensureLinkspectorBinaryIn(string $version, string $root): void
    {
        $bin = $root . DIRECTORY_SEPARATOR . self::LINKSPECTOR_BINARY_RELATIVE;
        if (self::isUsableLinkspectorBinary($bin)) {
            return;
        }

        if (is_file($bin)) { /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.isFile */
            unlink($bin);
        }

        self::ensureParentDirectory($bin);
        $url = sprintf(
            'https://github.com/UmbrellaDocs/linkspector/releases/download/%s/linkspector-linux-%s',
            $version,
            self::linkspectorAssetName(php_uname('m')),
        );

        if (! copy($url, $bin)) {
            throw new RuntimeException(sprintf('Unable to download linkspector from %s', $url));
        }

        chmod($bin, 0755);
    }

    /** @return list<string> */
    public static function discoverDocumentationFiles(string $root): array
    {
        if (is_dir($root . DIRECTORY_SEPARATOR . '.git')) { /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.isDir */
            $output = self::gitLsFilesMarkdown($root);
            if ($output !== null) {
                return self::splitNullSeparated($output);
            }
        }

        return self::findMarkdownFiles($root);
    }

    public static function linkspectorAssetName(string $machine): string
    {
        return match ($machine) {
            'x86_64' => 'x64',
            'aarch64', 'arm64' => 'arm64',
            default => 'x64',
        };
    }

    public static function projectRoot(): string
    {
        return dirname(__DIR__);
    }

    private static function isUsableLinkspectorBinary(string $bin): bool
    {
        if (! is_file($bin)) { /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.isFile */
            return false;
        }

        if (! is_executable($bin)) {
            return false;
        }

        $size = filesize($bin);
        if (! is_int($size) || $size < 1_024) {
            return false;
        }

        $header = file_get_contents($bin, false, null, 0, 4); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.fileGetContents */
        if (! is_string($header)) {
            return false;
        }

        return $header === "\x7fELF";
    }

    /** @return list<string> */
    private static function splitNullSeparated(string $raw): array
    {
        $parts = explode("\0", trim($raw, "\0"));
        if ($parts === ['']) {
            return [];
        }

        return array_values(array_filter($parts, static fn (string $part): bool => $part !== ''));
    }

    private static function gitLsFilesMarkdown(string $root): string|null
    {
        $process = proc_open(
            ['git', 'ls-files', '-z', '--cached', '--others', '--exclude-standard', '--', '*.md'],
            [
                ['pipe', 'r'],
                ['pipe', 'w'],
                ['pipe', 'w'],
            ],
            $pipes,
            $root,
        );

        if (! is_resource($process)) {
            return null;
        }

        fclose($pipes[0]); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.fclose */
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.fclose */
        fclose($pipes[2]); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.fclose */
        $exitCode = proc_close($process);

        if ($exitCode !== 0 || ! is_string($stdout)) {
            return null;
        }

        return $stdout;
    }

    /** @return list<string> */
    private static function findMarkdownFiles(string $root): array
    {
        $paths    = [];
        $rootReal = realpath($root);
        /** @phpstan-ignore-next-line identical.alwaysFalse */
        if ($rootReal === false) {
            return [];
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootReal, RecursiveDirectoryIterator::SKIP_DOTS),
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'md') {
                continue;
            }

            $relative = self::relativePath($rootReal, $file->getPathname());
            if (self::isIgnoredDocumentationPath($relative)) {
                continue;
            }

            $paths[] = $relative;
        }

        sort($paths);

        return $paths;
    }

    private static function isIgnoredDocumentationPath(string $relative): bool
    {
        return str_starts_with($relative, 'var' . DIRECTORY_SEPARATOR)
            || str_starts_with($relative, 'vendor' . DIRECTORY_SEPARATOR)
            || str_starts_with($relative, '.git' . DIRECTORY_SEPARATOR);
    }

    private static function relativePath(string $root, string $path): string
    {
        $prefix = $root . DIRECTORY_SEPARATOR;
        if (str_starts_with($path, $prefix)) {
            return substr($path, strlen($prefix));
        }

        return $path;
    }

    private static function recursiveCopy(string $source, string $destination): void
    {
        if (! is_dir($source)) { /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.isDir */
            throw new RuntimeException(sprintf('Source directory missing: %s', $source));
        }

        /** @phpstan-ignore-next-line wyrihaximus.reactphp.blocking.function.isDir, wyrihaximus.reactphp.blocking.function.mkdir */
        if (! is_dir($destination) && ! mkdir($destination, 0775, true) && ! is_dir($destination)) {
            throw new RuntimeException(sprintf('Unable to create directory: %s', $destination));
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST,
        );

        /** @var SplFileInfo $item */
        foreach ($iterator as $item) {
            $target = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathname();
            if ($item->isDir()) {
                /** @phpstan-ignore-next-line wyrihaximus.reactphp.blocking.function.isDir, wyrihaximus.reactphp.blocking.function.mkdir */
                if (! is_dir($target) && ! mkdir($target, 0775, true) && ! is_dir($target)) {
                    throw new RuntimeException(sprintf('Unable to create directory: %s', $target));
                }

                continue;
            }

            if (! copy($item->getPathname(), $target)) {
                throw new RuntimeException(sprintf('Unable to copy %s to %s', $item->getPathname(), $target));
            }
        }
    }

    private static function ensureParentDirectory(string $path): void
    {
        $directory = dirname($path);
        if (is_dir($directory)) { /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.isDir */
            return;
        }

        /** @phpstan-ignore-next-line wyrihaximus.reactphp.blocking.function.mkdir, wyrihaximus.reactphp.blocking.function.isDir */
        if (! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new RuntimeException(sprintf('Unable to create directory: %s', $directory));
        }
    }
}
