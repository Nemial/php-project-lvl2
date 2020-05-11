<?php


use PHPUnit\Framework\TestCase;
use function gendiff\Core\genDiff;
use function gendiff\Parser\parseFile;

class CoreTest extends TestCase
{
    public function testJson()
    {
        $pathToFileExpected = __DIR__ . "/fixtures/expected";
        $pathToFileBefore = __DIR__ . "/fixtures/json/before.json";
        $pathToFileAfter = __DIR__ . "/fixtures/json/after.json";
        $expected = file_get_contents($pathToFileExpected);
        $before = parseFile($pathToFileBefore);
        $after = parseFile($pathToFileAfter);
        $this->assertEquals($expected, genDiff($before, $after));
    }

    public function testYaml()
    {
        $pathToFileExpected = __DIR__ . "/fixtures/expected";
        $pathToFileBefore = __DIR__ . "/fixtures/yaml/before.yaml";
        $pathToFileAfter = __DIR__ . "/fixtures/yaml/after.yaml";
        $expected = file_get_contents($pathToFileExpected);
        $before = parseFile($pathToFileBefore);
        $after = parseFile($pathToFileAfter);
        $this->assertEquals($expected, genDiff($before, $after));
    }
}
