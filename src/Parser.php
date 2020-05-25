<?php

namespace gendiff\Parser;

use Symfony\Component\Yaml\Yaml;

function parse(string $contents, $format): object
{
    switch ($format) {
        case "json":
            return json_decode($contents);
        case "yml":
        case "yaml":
            return Yaml::parse($contents, Yaml::PARSE_OBJECT_FOR_MAP);
        default:
            throw new \Exception("Unsupported format to parse {$format}");
    }
}
