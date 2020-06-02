<?php

namespace gendiff\Ast;

use function Funct\Collection\union;

function makeNode($name, $type, $oldValue = null, $newValue = null, $children = null)
{
    return [
        "name" => $name,
        "type" => $type,
        "oldValue" => $oldValue,
        "newValue" => $newValue,
        "children" => $children
    ];
}

function getName($node)
{
    return $node["name"];
}

function getType($node)
{
    return $node["type"];
}

function getOldValue($node)
{
    return $node["oldValue"];
}

function getNewValue($node)
{
    return $node["newValue"];
}

function getChildren($node)
{
    return $node["children"];
}

function generateAst(object $before, object $after): array
{
    $dataBefore = get_object_vars($before);
    $dataAfter = get_object_vars($after);
    $keys = union(array_keys($dataBefore), array_keys($dataAfter));

    return array_map(
        function ($key) use ($before, $after) {
            if (!property_exists($after, $key)) {
                return makeNode($key, "removed", $before->$key);
            } elseif (!property_exists($before, $key)) {
                return makeNode($key, "added", null, $after->$key);
            } else {
                $beforeValue = $before->$key;
                $afterValue = $after->$key;
                if (!is_object($beforeValue) && !is_object($afterValue)) {
                    if ($beforeValue !== $afterValue) {
                        return makeNode($key, "changed", $beforeValue, $afterValue);
                    }
                    return makeNode($key, "unchanged", $beforeValue);
                } else {
                    return makeNode(
                        $key,
                        "object",
                        null,
                        null,
                        generateAst($beforeValue, $afterValue),
                    );
                }
            }
        },
        $keys
    );
}
