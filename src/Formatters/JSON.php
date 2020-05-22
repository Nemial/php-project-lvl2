<?php

namespace gendiff\Formatters\JSON;

use function gendiff\Ast\{
    getChildren,
    getName,
    getType,
};

function render(array $tree): array
{
    return array_reduce(
        $tree,
        function ($acc, $node) {
            $name = getName($node);
            if (getType($node) === "object") {
                $acc[$name] = render(getChildren($node));
                return $acc;
            }
            switch (getType($node)) {
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
