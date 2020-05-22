<?php

namespace gendiff\Parser;

use Symfony\Component\Yaml\Yaml;

function parseFileContents(string $fileContents, $fileExtension): object
{
    switch ($fileExtension) {
        case "json":
            return json_decode($fileContents);
        case "yaml":
            return Yaml::parse($fileContents, Yaml::PARSE_OBJECT_FOR_MAP);
        default:
            break;
    }
}
