<?php declare(strict_types = 1);

namespace PHPStan\File;

use PHPStan\Testing\PHPStanTestCase;

class FileExcluderTest extends PHPStanTestCase
{

	/**
	 * @dataProvider dataExcludeOnWindows
	 * @param string[] $analyseExcludes
	 */
	public function testFilesAreExcludedFromAnalysingOnWindows(
		string $filePath,
		array $analyseExcludes,
		bool $isExcluded,
	): void
	{
		$this->skipIfNotOnWindows();

		$fileExcluder = new FileExcluder($this->getFileHelper(), $analyseExcludes, false);

		$this->assertSame($isExcluded, $fileExcluder->isExcludedFromAnalysing($filePath));
	}

	public function dataExcludeOnWindows(): array
	{
		return [
			[
				__DIR__ . '/data/excluded-file.php',
				[],
				false,
			],
			[
				__DIR__ . '/data/excluded-file.php',
				[__DIR__],
				true,
			],
			[
				__DIR__ . '\Foo\data\excluded-file.php',
				[__DIR__ . '/*\/data/*'],
				true,
			],
			[
				__DIR__ . '\data\func-call.php',
				[],
				false,
			],
			[
				__DIR__ . '\data\parse-error.php',
				[__DIR__ . '/*'],
				true,
			],
			[
				__DIR__ . '\data\parse-error.php',
				[__DIR__ . '/data/?a?s?-error.?h?'],
				true,
			],
			[
				__DIR__ . '\data\parse-error.php',
				[__DIR__ . '/data/[pP]arse-[eE]rror.ph[pP]'],
				true,
			],
			[
				__DIR__ . '\data\parse-error.php',
				['tests/PHPStan/File/data'],
				true,
			],
			[
				__DIR__ . '\data\parse-error.php',
				[__DIR__ . '/aaa'],
				false,
			],
			[
				'C:\Temp\data\parse-error.php',
				['C:/Temp/*'],
				true,
			],
			[
				'C:\Data\data\parse-error.php',
				['C:/Temp/*'],
				false,
			],
			[
				'c:\Temp\data\parse-error.php',
				['C:/Temp/*'],
				true,
			],
			[
				'C:\Temp\data\parse-error.php',
				['C:/temp/*'],
				true,
			],
			[
				'c:\Data\data\parse-error.php',
				['C:/Temp/*'],
				false,
			],
			[
				'c:\etc\phpstan\dummy-1.php',
				['c:\etc\phpstan\\'],
				true,
			],
			[
				'c:\etc\phpstan-test\dummy-2.php',
				['c:\etc\phpstan\\'],
				false,
			],
			[
				'c:\etc\phpstan-test\dummy-2.php',
				['c:\etc\phpstan'],
				true,
			],
		];
	}

	/**
	 * @dataProvider dataExcludeOnUnix
	 * @param string[] $analyseExcludes
	 */
	public function testFilesAreExcludedFromAnalysingOnUnix(
		string $filePath,
		array $analyseExcludes,
		bool $isExcluded,
	): void
	{
		$this->skipIfNotOnUnix();

		$fileExcluder = new FileExcluder($this->getFileHelper(), $analyseExcludes, false);

		$this->assertSame($isExcluded, $fileExcluder->isExcludedFromAnalysing($filePath));
	}

	public function dataExcludeOnUnix(): array
	{
		return [
			[
				__DIR__ . '/data/excluded-file.php',
				[],
				false,
			],
			[
				__DIR__ . '/data/excluded-file.php',
				[__DIR__],
				true,
			],
			[
				__DIR__ . '/Foo/data/excluded-file.php',
				[__DIR__ . '/*/data/*'],
				true,
			],
			[
				__DIR__ . '/data/func-call.php',
				[],
				false,
			],
			[
				__DIR__ . '/data/parse-error.php',
				[__DIR__ . '/*'],
				true,
			],
			[
				__DIR__ . '/data/parse-error.php',
				[__DIR__ . '/data/?a?s?-error.?h?'],
				true,
			],
			[
				__DIR__ . '/data/parse-error.php',
				[__DIR__ . '/data/[pP]arse-[eE]rror.ph[pP]'],
				true,
			],
			[
				__DIR__ . '/data/parse-error.php',
				['tests/PHPStan/File/data'],
				true,
			],
			[
				__DIR__ . '/data/parse-error.php',
				[__DIR__ . '/aaa'],
				false,
			],
			[
				'/tmp/data/parse-error.php',
				['/tmp/*'],
				true,
			],
			[
				'/home/myname/data/parse-error.php',
				['/tmp/*'],
				false,
			],
			[
				'/etc/phpstan/dummy-1.php',
				['/etc/phpstan/'],
				true,
			],
			[
				'/etc/phpstan-test/dummy-2.php',
				['/etc/phpstan/'],
				false,
			],
			[
				'/etc/phpstan-test/dummy-2.php',
				['/etc/phpstan'],
				true,
			],
		];
	}

	public function dataNoImplicitWildcard(): iterable
	{
		yield [
			__DIR__ . '/tests/foo.php',
			[
				__DIR__ . '/test',
			],
			false,
			true,
		];

		yield [
			__DIR__ . '/tests/foo.php',
			[
				__DIR__ . '/test',
			],
			true,
			false,
		];

		yield [
			__DIR__ . '/test/foo.php',
			[
				__DIR__ . '/test',
			],
			true,
			true,
		];

		yield [
			__DIR__ . '/FileExcluderTest.php',
			[
				__DIR__ . '/FileExcluderTest.php',
			],
			true,
			true,
		];

		yield [
			__DIR__ . '/tests/foo.php',
			[
				__DIR__ . '/test*',
			],
			true,
			true,
		];
	}

	/**
	 * @dataProvider dataNoImplicitWildcard
	 * @param string[] $analyseExcludes
	 */
	public function testNoImplicitWildcard(
		string $filePath,
		array $analyseExcludes,
		bool $noImplicitWildcard,
		bool $isExcluded,
	): void
	{
		$this->skipIfNotOnUnix();

		$fileExcluder = new FileExcluder($this->getFileHelper(), $analyseExcludes, $noImplicitWildcard);

		$this->assertSame($isExcluded, $fileExcluder->isExcludedFromAnalysing($filePath));
	}

}
