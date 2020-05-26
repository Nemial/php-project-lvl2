<?php

namespace gendiff\Parser;

use Symfony\Component\Yaml\Yaml;

function parse(string $data, $format): object
{
    switch ($format) {
        case "json":
            return json_decode($data);
        case "yml":
        case "yaml":
            return Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP);
        default:
            throw new \Exception("Unsupported format to parse {$format}");
    }
}
