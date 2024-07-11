<?php

$host = 'localhost';
$db = 'solomono2';
$user = 'root';
$pass = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Помилка підключення: " . $e->getMessage();
    exit;
}

// Функція для отримання всіх категорій
function getCategories($pdo)
{
    $stmt = $pdo->query('SELECT * FROM categories ORDER BY categories_id');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Функція для побудови дерева категорій
function buildTree(array $categories): array
{
    $tree = [];
    $references = [];

    foreach ($categories as $category) {
        $categoryId = $category['categories_id'];
        $parentId = $category['parent_id'];

        if ($parentId == 0) {
            if (!isset($tree[$categoryId])) {
                $tree[$categoryId] = $categoryId;
            }
        } else {
            if (!isset($references[$parentId])) {
                $references[$parentId] = [];
            }
            if (!is_array($references[$parentId])) {
                $references[$parentId] = [$references[$parentId]];
            }
            $references[$parentId][$categoryId] = $categoryId;
        }
    }

    foreach ($references as $parentId => $children) {
        if (isset($tree[$parentId])) {
            $tree[$parentId] = $children;
        } else {
            foreach ($tree as &$node) {
                if (is_array($node) && isset($node[$parentId])) {
                    $node[$parentId] = $children;
                    break;
                }
            }
        }
    }

    return $tree;
}

// Отримання всіх категорій з бази даних
$categories = getCategories($pdo);

$start = microtime(true);

// Побудова дерева категорій
$tree = buildTree($categories);

// Вивід дерева категорій
echo '<pre>';
print_r($tree);
echo '</pre>';

echo 'Час виконання скрипту: ' . round(microtime(true) - $start, 4) . ' сек.';

// php build_tree_v2.php - для запуску скрипта