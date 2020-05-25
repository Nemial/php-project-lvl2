<?php

namespace gendiff\Formatters\JSON;

function render(array $tree): string
{
    return json_encode($tree) . "\n";
}
