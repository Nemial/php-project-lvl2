<?php

namespace gendiff\tests;

use PHPUnit\Framework\TestCase;

use function gendiff\Core\genDiff;

class CoreTest extends TestCase
{
    public function makePathToFixtures($file)
    {
        $parts = [__DIR__, "fixtures", $file];
        return implode("/", $parts);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGenDiff($beforeFileName, $afterFileName, $expectedFileName, $format)
    {
        $before = $this->makePathToFixtures($beforeFileName);
        $after = $this->makePathToFixtures($afterFileName);
        $expected = $this->makePathToFixtures($expectedFileName);
        $this->assertEquals(trim(file_get_contents($expected)), genDiff($before, $after, $format));
    }

    public function dataProvider()
    {
        return [
            "yamlFormat" => [
                "before.yaml",
                "after.yaml",
                "flatExpected",
                "pretty"
            ],
            "prettyFormat" => [
                "before.json",
                "after.json",
                "prettyExpected",
                "pretty"
            ],
            "plainFormat" => [
                "before.json",
                "after.json",
                "plainExpected",
                "plain"
            ],
            "jsonFormat" => [
                "before.json",
                "after.json",
                "jsonExpected.json",
                "json"
            ]
        ];
    }
}
