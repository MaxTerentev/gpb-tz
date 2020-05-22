<?php


namespace App\Helpers\Contracts;


interface TreeBuilder
{
    public function buildTreeArray(array $nodes, ?int $parentId = null) : array;

    public function buildTreeWidget(array $tree) : string;

    public function prepareTreeNodes(array $nodes) : array;
}