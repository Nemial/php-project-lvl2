<?php

namespace gendiff\File;

function normalizePathToFile(string $pathToFile): string
{
    if (file_exists($pathToFile)) {
        return $pathToFile;
    }

    return getcwd() . DIRECTORY_SEPARATOR . $pathToFile;
}

function getFileExtension(string $file): string
{
    return pathinfo(normalizePathToFile($file), PATHINFO_EXTENSION);
}

function getFileContents(string $file): string
{
    return file_get_contents(normalizePathToFile($file));
}
