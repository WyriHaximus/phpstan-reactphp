<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\React\PHPStan\Utils;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use WyriHaximus\React\PHPStan\Utils\DocumentationQa;
use WyriHaximus\TestUtilities\TestCase;

use function chmod;
use function dirname;
use function file_get_contents;
use function file_put_contents;
use function filesize;
use function mkdir;
use function sprintf;
use function str_repeat;
use function sys_get_temp_dir;
use function uniqid;

use const DIRECTORY_SEPARATOR;

final class DocumentationQaTest extends TestCase
{
    public function testRunRejectsUnknownCommand(): void
    {
        try {
            DocumentationQa::run('nope');
            self::fail('Expected InvalidArgumentException');
        } catch (InvalidArgumentException $exception) {
            self::assertSame('Unknown documentation-qa command: nope', $exception->getMessage());
        }
    }

    #[DataProvider('linkspectorAssetNameProvider')]
    public function testLinkspectorAssetName(string $machine, string $expected): void
    {
        self::assertSame($expected, DocumentationQa::linkspectorAssetName($machine));
    }

    /** @return iterable<string, array{string, string}> */
    public static function linkspectorAssetNameProvider(): iterable
    {
        yield 'x64' => ['x86_64', 'x64'];
        yield 'arm64' => ['aarch64', 'arm64'];
        yield 'arm64 alias' => ['arm64', 'arm64'];
        yield 'default' => ['ppc64', 'x64'];
    }

    public function testDiscoverDocumentationFilesWithoutGitUsesFind(): void
    {
        $root = $this->temporaryProjectRoot();
        mkdir($root . '/docs', 0775, true); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.mkdir */
        file_put_contents($root . '/docs/a.md', '# A'); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.filePutContents */
        file_put_contents($root . '/README.md', '# R'); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.filePutContents */
        mkdir($root . '/var/nope', 0775, true); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.mkdir */
        file_put_contents($root . '/var/nope/skip.md', '# skip'); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.filePutContents */

        $files = DocumentationQa::discoverDocumentationFiles($root);

        self::assertSame(['README.md', 'docs' . DIRECTORY_SEPARATOR . 'a.md'], $files);
    }

    public function testWriteDocumentationFilesListAndPrepareLinkspectorConfig(): void
    {
        $root = $this->temporaryProjectRoot();
        mkdir($root . '/etc/ci', 0775, true); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.mkdir */
        mkdir($root . '/etc/qa/vale/vocabularies/ReactPHP', 0775, true); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.mkdir */
        file_put_contents($root . '/etc/ci/linkspector.options.yml', "timeout: 1\n"); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.filePutContents */
        file_put_contents($root . '/README.md', '# R'); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.filePutContents */
        file_put_contents($root . '/etc/qa/vale/vocabularies/ReactPHP/accept.txt', "ReactPHP\n"); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.filePutContents */

        DocumentationQa::writeDocumentationFilesListIn($root);
        DocumentationQa::installValeVocabIn($root);
        DocumentationQa::prepareLinkspectorConfigIn($root);

        self::assertFileExists($root . '/var/ci/documentation-files.lst');
        self::assertFileExists($root . '/var/vale/styles/config/vocabularies/ReactPHP/accept.txt');
        $config = (string) file_get_contents($root . '/var/ci/linkspector.yml'); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.fileGetContents */
        self::assertStringContainsString('files:', $config);
        self::assertStringContainsString('- README.md', $config);
        self::assertStringContainsString('timeout: 1', $config);
    }

    public function testPrepareLinkspectorConfigRequiresFileList(): void
    {
        try {
            DocumentationQa::prepareLinkspectorConfigIn($this->temporaryProjectRoot());
            self::fail('Expected RuntimeException');
        } catch (RuntimeException $exception) {
            self::assertSame('Missing documentation file list; run files-cache first.', $exception->getMessage());
        }
    }

    public function testInstallValeVocabRequiresSourceDirectory(): void
    {
        $root = $this->temporaryProjectRoot();

        try {
            DocumentationQa::installValeVocabIn($root);
            self::fail('Expected RuntimeException');
        } catch (RuntimeException $exception) {
            self::assertSame(
                sprintf('Source directory missing: %s', $root . '/etc/qa/vale/vocabularies'),
                $exception->getMessage(),
            );
        }
    }

    public function testEnsureLinkspectorBinarySkipsWhenExecutableExists(): void
    {
        $root = $this->temporaryProjectRoot();
        $bin  = $root . '/var/linkspector/linkspector';
        mkdir(dirname($bin), 0775, true); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.mkdir */
        file_put_contents($bin, "\x7fELF" . str_repeat('x', 2_000)); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.filePutContents */
        chmod($bin, 0755);
        $sizeBefore = filesize($bin);

        DocumentationQa::ensureLinkspectorBinaryIn('v0.5.6', $root);

        self::assertSame($sizeBefore, filesize($bin));
    }

    public function testDiscoverDocumentationFilesInGitRepository(): void
    {
        $files = DocumentationQa::discoverDocumentationFiles(DocumentationQa::projectRoot());

        self::assertContains('README.md', $files);
    }

    public function testRunDispatchesKnownCommandsOnProjectRoot(): void
    {
        DocumentationQa::run('files-cache');
        DocumentationQa::run('vale-vocab');

        self::assertFileExists(DocumentationQa::projectRoot() . '/var/ci/documentation-files.lst');
        self::assertFileExists(DocumentationQa::projectRoot() . '/var/vale/styles/config/vocabularies/ReactPHP/accept.txt');
    }

    private function temporaryProjectRoot(): string
    {
        $root = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'documentation-qa-' . uniqid('', true);
        mkdir($root, 0775, true); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.mkdir */

        return $root;
    }
}
