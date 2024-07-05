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

// Функція для побудови дерева категорій ітеративним способом
function buildTree(array $categories): array
{
    $tree = [];
    $references = [];

    foreach ($categories as $category) {
        $references[$category['categories_id']] = $category;
        $references[$category['categories_id']]['children'] = array();
    }

    foreach ($references as $categoryId => $category) {
        if ($category['parent_id'] == 0) {
            $tree[$categoryId] = &$references[$categoryId];
        } else {
            $references[$category['parent_id']]['children'][$categoryId] = &$references[$categoryId];
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