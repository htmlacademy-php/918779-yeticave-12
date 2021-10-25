<?php

define('HOUR', '3600');
define('MINUTE', '60');

$is_auth = rand(0, 1);

$user_name = 'Антон';

$title = 'Главная';

$categories = [ 'Доски и лыжи', 'Крепления', 'Ботинки', 'Одежда', 'Инструменты', 'Разное'];

$lots = [

    [
        'title' => '2014 Rossignol District Snowboard',
        'category' => 'Доски и лыжи',
        'price' => 10999,
        'path' => 'img/lot-1.jpg',
        'expiration' => '2021-10-26 21:17'
    ],

    [
        'title' => 'DC Ply Mens 2016/2017 Snowboard',
        'category' => 'Доски и лыжи',
        'price' => 159999,
        'path' => 'img/lot-2.jpg',
        'expiration' => '2021-11-01'
    ],

    [
        'title' => 'Крепления Union Contact Pro 2015 года размер L/XL',
        'category' => 'Крепления',
        'price' => 	8000,
        'path' => 'img/lot-3.jpg',
        'expiration' => '2021-10-28'
    ],

    [
        'title' => 'Ботинки для сноуборда DC Mutiny Charocal',
        'category' => 'Ботинки',
        'price' => 10999,
        'path' => 'img/lot-4.jpg',
        'expiration' => '2021-10-29'
    ],

    [
        'title' => 'Куртка для сноуборда DC Mutiny Charocal',
        'category' => 'Одежда',
        'price' => 7500,
        'path' => 'img/lot-5.jpg',
        'expiration' => '2021-10-31'
    ],

    [
        'title' => 'Маска Oakley Canopy',
        'category' => 'Разное',
        'price' => 5400,
        'path' => 'img/lot-6.jpg',
        'expiration' => '2021-10-30'
    ]

];

?>