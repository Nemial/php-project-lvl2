<?php


use PHPUnit\Framework\TestCase;
use function gendiff\Core\{genDiff, getFileContents, decodeFileContents};

class CoreTest extends TestCase
{
    public function testJson()
    {
        $pathToFileExpected = __DIR__ . "/fixtures/json/expected";
        $pathToFileBefore = __DIR__ . "/fixtures/json/before.json";
        $pathToFileAfter = __DIR__ . "/fixtures/json/after.json";
        $expected = getFileContents($pathToFileExpected);
        $before = decodeFileContents(getFileContents($pathToFileBefore));
        $after = decodeFileContents(getFileContents($pathToFileAfter));
        $this->assertEquals($expected, genDiff($before, $after));
    }
}
