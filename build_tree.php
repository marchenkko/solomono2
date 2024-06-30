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
function buildTree(array $categories, $parentId = 0)
{
    $branch = array();

    foreach ($categories as $category) {
        if ($category['parent_id'] == $parentId) {
            $children = buildTree($categories, $category['categories_id']);
            if ($children) {
                $branch[$category['categories_id']] = $children;
            } else {
                $branch[$category['categories_id']] = $category['categories_id'];
            }
        }
    }

    return $branch;
}

// Отримання всіх категорій з бази даних
$categories = getCategories($pdo);

// Побудова дерева категорій
$tree = buildTree($categories);

// Вивід дерева категорій
echo '<pre>';
print_r($tree);
echo '</pre>';

//php build_tree.php - для запуску скрипта
