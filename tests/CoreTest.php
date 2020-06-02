<?php

namespace gendiff\tests;

use PHPUnit\Framework\TestCase;

use function gendiff\Core\genDiff;

class CoreTest extends TestCase
{
    public function makePathToFixtures($file)
    {
        $parts = [__DIR__, "/fixtures/", $file];
        return implode("", $parts);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGenDiff($beforeFileName, $afterFileName, $expected, $format)
    {
        $before = $this->makePathToFixtures($beforeFileName);
        $after = $this->makePathToFixtures($afterFileName);
        $this->assertEquals(trim(file_get_contents($expected)), genDiff($before, $after, $format));
    }

    public function dataProvider()
    {
        return [
            "yamlFormat" => [
                "before.yaml",
                "after.yaml",
                __DIR__ . "/fixtures/flatExpected",
                "pretty"
            ],
            "prettyFormat" => [
                "before.json",
                "after.json",
                __DIR__ . "/fixtures/prettyExpected",
                "pretty"
            ],
            "plainFormat" => [
                "before.json",
                "after.json",
                __DIR__ . "/fixtures/plainExpected",
                "plain"
            ],
            "jsonFormat" => [
                "before.json",
                "after.json",
                __DIR__ . "/fixtures/jsonExpected.json",
                "json"
            ]
        ];
    }
}
