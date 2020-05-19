<?php

namespace gendiff\Formatters\Plain;

use function gendiff\Ast\{
    getChildren,
    getOldValue,
    getNewValue,
    getPath,
    getType,
    getStatus,
    haveChildren
};

function render(array $tree): array
{
    return array_reduce(
        $tree,
        function ($acc, $node) {
            if (getType($node) === "node" && haveChildren($node)) {
                $acc[] = render(getChildren($node));
                return $acc;
            }
            $path = getPath($node);
            $oldValue = is_array(getOldValue($node)) ? "complex value" : getOldValue($node);
            switch (getStatus($node)) {
                case "changed":
                    $newValue = getNewValue($node);
                    $acc[] = "Property '{$path}' was changed. From '{$oldValue}' to '{$newValue}'";
                    break;
                case "added":
                    $acc[] = "Property '{$path}' was added with value: '{$oldValue}'";
                    break;
                case "removed":
                    $acc[] = "Property '{$path}' was removed";
                    break;
                default:
                    break;
            }
            return $acc;
        },
        []
    );
}
