<?php


use app\ReverseTemplate;

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


require  __DIR__ . '../vendor/autoload.php';

$template = 'Hello, my name is {{name}}.';
$result = 'Hello, my name is .';
$options = [];

try {
    $reverse_template = new ReverseTemplate($template, $result, $options);
    print_r($reverse_template->revers());
} catch (Exception $e) {
    print_r($e->getMessage());
}
