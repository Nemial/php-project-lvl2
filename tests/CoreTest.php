<?php

namespace gendiff\tests;

use PHPUnit\Framework\TestCase;

use function gendiff\Core\genDiff;

class CoreTest extends TestCase
{
    public function testYaml()
    {
        $pathToFileExpected = __DIR__ . "/fixtures/flatExpected";
        $pathToFileBefore = __DIR__ . "/fixtures/yaml/before.yaml";
        $pathToFileAfter = __DIR__ . "/fixtures/yaml/after.yaml";
        $expected = trim(file_get_contents($pathToFileExpected));
        $this->assertEquals($expected, genDiff($pathToFileBefore, $pathToFileAfter));
    }

    /**
     * @dataProvider jsonProvider
     */
    public function testJSON($before, $after, $expected, $format)
    {
        $this->assertEquals($expected, genDiff($before, $after, $format));
    }

    public function jsonProvider()
    {
        return [
            "prettyFormat" => [
                __DIR__ . "/fixtures/json/before.json",
                __DIR__ . "/fixtures/json/after.json",
                trim(file_get_contents(__DIR__ . "/fixtures/prettyExpected")),
                "pretty"
            ],
            "plainFormat" => [
                __DIR__ . "/fixtures/json/before.json",
                __DIR__ . "/fixtures/json/after.json",
                trim(file_get_contents(__DIR__ . "/fixtures/plainExpected")),
                "plain"
            ],
            "jsonFormat" => [
                __DIR__ . "/fixtures/json/before.json",
                __DIR__ . "/fixtures/json/after.json",
                trim(file_get_contents(__DIR__ . "/fixtures/jsonExpected.json")),
                "json"
            ]
        ];
    }
}
