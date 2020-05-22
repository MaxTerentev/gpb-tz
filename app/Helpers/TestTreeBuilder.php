<?php


namespace App\Helpers;


use App\Helpers\Contracts\TreeBuilder;
use App\TreeNode;
use Illuminate\Support\Facades\View;

class TestTreeBuilder implements TreeBuilder
{
    /**
     * Сформировать массив дерева
     * @param array $nodes  Массив нод
     * @param int $parentId  ID родителя, от которого строим дерево (по умолчанию используется корень)
     * @return array    Массив дерева
     */
    public function buildTreeArray(array $nodes, ?int $parentId = null) : array
    {
        $tree = [];
        foreach ($nodes as $node)
        {
            // Подбираем детей текущего родителя
            if (isset($node['id']) && isset($node['position']) && $parentId == $node['parent_id']) { //TODO:ынести валидацию ноды
                // Ищем детей текущей ноды
                $childs = $this->buildTreeArray($nodes , $node['id']);
                if (!empty($childs)) { // Проверям наличие детей
                    // Присваем значение самой ноды
                    $tree[$node['position']] = $node;

                    // Отсортируем
                    ksort($childs);

                    // Добавляем ветку детей
                    $tree[$node['position']]['childs'] = $childs;
                } else {
                    // Конечная нода дерева
                    $tree[$node['position']] = $node;
                }
            }
        }
        return $tree;
    }

    /**
     * Сформировать виджет дерева
     * @param array $tree   Массив дерева
     * @return string   HTML дерева
     */
    public function buildTreeWidget(array $tree) : string
    {
        return View::make('tree.widget', ['tree' => $tree, 'currency' => TreeNode::CURRENCY])->render();
    }

    /**
     * Подготовка массива нод к определенному формату (добавляем id и parent_id, а также корректируем value для БД)
     * @param array $nodes  Массив нод дерева
     * @return array    Отформатированный массив нод
     */
    public function prepareTreeNodes(array $nodes) : array
    {
        if (!empty($nodes)) {
            // Заранее сформируем id и parent_id для каждой ноды, чтобы избежать лишних запросов в базу
            $nodes = $this->generateNodeIds($nodes);

            // Форматируем значение ноды
            array_walk($nodes, function (&$node) {
                $node['value'] = str_replace(' ₽', '', $node['value']);
                $node['value'] = floatval($node['value']);
            });
        }
        return $nodes;
    }

    /**
     * Генерирует id и определяет parent_id для каждой ноды дерева
     * @param array $nodes  Массив нод дерева
     * @return array    Массив нод дерева (с полями id и parent_id)
     */
    protected function generateNodeIds(array $nodes) : array
    {
        if (!empty($nodes)) {
            $idList = array_column($nodes, 'position');
            if (!empty($idList)) {
//                natsort($idList);
//                $idList = array_values($idList);
                // Сформируем список ID для каждой позиции (массив ввида ['{position}' => '{id}'])
                $idList = array_flip($idList);
                dump($idList);

                // Присваивание id и parent_id нодам
                array_walk($nodes, function (&$node, $key, $idList) {
                    // Присвоим ID для кажой ноды
                    $node['id'] = $idList[$node['position']] + 1;

                    // Определим и присвоим ID родителя
                    if (strlen($node['position']) == 1) {
                        $node['parent_id'] = null;
                    } else {
                        $parent = preg_replace('/\..$/', '', $node['position']);
                        $node['parent_id'] = $idList[$parent] + 1;
                    }
                }, $idList);
            }
        }

        return $nodes;
    }
}