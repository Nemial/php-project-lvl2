<?php

use PHPUnit\Framework\TestCase;

use function gendiff\Core\genDiff;
use function gendiff\Parser\parseFile;

class CoreTest extends TestCase
{

    public function testFlatJson()
    {
        $pathToFileExpected = __DIR__ . "/fixtures/flatExpected";
        $pathToFileBefore = __DIR__ . "/fixtures/flatJson/before.json";
        $pathToFileAfter = __DIR__ . "/fixtures/flatJson/after.json";
        $expected = file_get_contents($pathToFileExpected);
        $before = parseFile($pathToFileBefore);
        $after = parseFile($pathToFileAfter);
        $this->assertEquals($expected, genDiff($before, $after));
    }

    public function testFlatYaml()
    {
        $pathToFileExpected = __DIR__ . "/fixtures/flatExpected";
        $pathToFileBefore = __DIR__ . "/fixtures/yaml/before.yaml";
        $pathToFileAfter = __DIR__ . "/fixtures/yaml/after.yaml";
        $expected = file_get_contents($pathToFileExpected);
        $before = parseFile($pathToFileBefore);
        $after = parseFile($pathToFileAfter);
        $this->assertEquals($expected, genDiff($before, $after));
    }

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
}
