<?php

namespace gendiff\Formatters\JSON;

use function gendiff\Ast\{
    getChildren,
    getName,
    getType,
    getStatus,
    haveChildren
};

function render(array $tree): array
{
    return array_reduce(
        $tree,
        function ($acc, $node) {
            $name = getName($node);
            if (getType($node) === "node" && haveChildren($node)) {
                $acc[$name] = render(getChildren($node));
                return $acc;
            }
            switch (getStatus($node)) {
                case "changed":
                    $acc[$name] = "changed";
                    break;
                case "removed":
                    $acc[$name] = "removed";
                    break;
                case "added":
                    $acc[$name] = "added";
                    break;
                default:
                    break;
            }
            return $acc;
        },
        []
    );
}
