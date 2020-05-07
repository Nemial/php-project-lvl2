<?php

namespace gendiff\Core;

use function funct\false;

function normalizePathToFile(string $pathToFile): string
{
    if (file_exists($pathToFile)) {
        return $pathToFile;
    }

    return getcwd() . DIRECTORY_SEPARATOR . $pathToFile;
}

function getFileContents(string $pathToFile): string
{
    return file_get_contents($pathToFile);
}

function decodeFile(string $file): array
{
    return json_decode($file, true);
}

function genDiff(array $firstFile, array $secondFile)
{
    $keysFirstFile = array_keys($firstFile);
    $keysSecondFile = array_keys($secondFile);
    $allKeys = array_unique(array_merge($keysFirstFile, $keysSecondFile));

    $diffMap = array_reduce($allKeys, function ($acc, $key) use ($firstFile, $secondFile) {
        $haveFirstFileKey = array_key_exists($key, $firstFile);
        $haveSecondFileKey = array_key_exists($key, $secondFile);
        $firstValue = $firstFile[$key] ?? null;
        $secondValue = $secondFile[$key] ?? null;
        if (is_bool($firstValue)) {
            $firstValue = false($firstValue) ? 'false' : 'true';
        }
        if (is_bool($secondValue)) {
            $secondValue = false($secondValue) ? 'false' : 'true';
        }

        if ($haveFirstFileKey && $haveSecondFileKey) {
            if ($firstValue === $secondValue) {
                $acc[] = "     {$key}: {$firstValue}";
            } else {
                $acc[] = "   + {$key}: {$firstValue}";
                $acc[] = "   - {$key}: {$secondValue}";
            }
        } elseif (!$haveFirstFileKey) {
            $acc[] = "   + {$key}: {$secondValue}";
        } else {
            $acc[] = "   - {$key}: {$firstValue}";
        }

        return $acc;
    }, []);
    array_unshift($diffMap, '{');
    array_push($diffMap, '}');

    return implode(PHP_EOL, $diffMap) . PHP_EOL;
}
