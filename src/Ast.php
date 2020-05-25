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
                return makeNode($key, "added", $after->$key);
            } else {
                if (is_object($before->$key) && is_object($after->$key)) {
                    return makeNode(
                        $key,
                        "object",
                        null,
                        null,
                        generateAst($before->$key, $after->$key),
                    );
                } elseif ($before->$key !== $after->$key) {
                    return makeNode($key, "changed", $before->$key, $after->$key);
                } else {
                    return makeNode($key, "unchanged", $before->$key);
                }
            }
        },
        $keys
    );
}
