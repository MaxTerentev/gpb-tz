<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class TreeNode extends Model
{
    // Валюта (используется при рендере)
    const CURRENCY = '₽';

    public $timestamps = false;

    protected $fillable = [
        'parent_id',
        'title',
        'position',
        'value'
    ];

    public function parent()
    {
        return $this->belongsTo(TreeNode::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(TreeNode::class, 'parent_id');
    }

    /**
     * Сохранить массива нод в базу
     * @param array $nodes Массив нод
     * @return bool Результат выполнения запроса
     */
    public static function saveTreeNodes(array $nodes) : bool
    {
        return self::insert($nodes);
    }

    /**
     *  Полчить ноды из базы ввиде массива
     */
    public static function getTreeNodesAsArray() : array
    {
        return TreeNode::all()->sortBy('position')->toArray();
    }

    /**
     *  Полчить коллекцию нод
     */
//    public static function getTreeNodes() : Collection
//    {
//        return TreeNode::all()->sortBy('position');
//    }
}
