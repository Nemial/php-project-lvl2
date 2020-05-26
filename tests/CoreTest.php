<?php

namespace gendiff\tests;

use PHPUnit\Framework\TestCase;

use function gendiff\Core\genDiff;
use function gendiff\File\getFileContents;

class CoreTest extends TestCase
{

    public function testFlatJson()
    {
        $pathToFileExpected = __DIR__ . "/fixtures/flatExpected";
        $pathToFileBefore = __DIR__ . "/fixtures/flatJson/before.json";
        $pathToFileAfter = __DIR__ . "/fixtures/flatJson/after.json";
        $expected = trim(getFileContents($pathToFileExpected));
        $this->assertEquals($expected, genDiff($pathToFileBefore, $pathToFileAfter));
    }

    public function testFlatYaml()
    {
        $pathToFileExpected = __DIR__ . "/fixtures/flatExpected";
        $pathToFileBefore = __DIR__ . "/fixtures/yaml/before.yaml";
        $pathToFileAfter = __DIR__ . "/fixtures/yaml/after.yaml";
        $expected = trim(getFileContents($pathToFileExpected));
        $this->assertEquals($expected, genDiff($pathToFileBefore, $pathToFileAfter));
    }

    public function testPrettyFormat()
    {
        $pathToFileExpected = __DIR__ . "/fixtures/prettyExpected";
        $pathToFileBefore = __DIR__ . "/fixtures/json/before.json";
        $pathToFileAfter = __DIR__ . "/fixtures/json/after.json";
        $expected = trim(getFileContents($pathToFileExpected));
        $this->assertEquals($expected, genDiff($pathToFileBefore, $pathToFileAfter));
    }

    public function testPlainFormat()
    {
        $pathToFileExpected = __DIR__ . "/fixtures/plainExpected";
        $pathToFileBefore = __DIR__ . "/fixtures/json/before.json";
        $pathToFileAfter = __DIR__ . "/fixtures/json/after.json";
        $expected = trim(getFileContents($pathToFileExpected));
        $this->assertEquals($expected, genDiff($pathToFileBefore, $pathToFileAfter, "plain"));
    }

    public function testJSONFormat()
    {
        $pathToFileExpected = __DIR__ . "/fixtures/jsonExpected.json";
        $pathToFileBefore = __DIR__ . "/fixtures/json/before.json";
        $pathToFileAfter = __DIR__ . "/fixtures/json/after.json";
        $expected = trim(getFileContents($pathToFileExpected));
        $this->assertEquals($expected, genDiff($pathToFileBefore, $pathToFileAfter, "json"));
    }
}
