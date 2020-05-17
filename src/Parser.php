<?php

namespace gendiff\Parser;

use Symfony\Component\Yaml\Yaml;

function parseFile(string $pathToFile): object
{
    $fileExtension = pathinfo($pathToFile, PATHINFO_EXTENSION);
    $fileContents = file_get_contents($pathToFile);

    switch ($fileExtension) {
        case "json":
            return json_decode($fileContents);
        case "yaml":
            return Yaml::parse($fileContents, Yaml::PARSE_OBJECT_FOR_MAP);
        default:
            break;
    }
}

function normalizePathToFile(string $pathToFile): string
{
    if (file_exists($pathToFile)) {
        return $pathToFile;
    }

    return getcwd() . DIRECTORY_SEPARATOR . $pathToFile;
}
