<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->group(
    ['prefix'=>'api/'],
    function () use ($router) {
        $router->get(
            'transaction',
            ['uses' => 'TransactionController@getAllTransaction']
        );
        $router->get(
            'transaction/{id}',
            ['uses' => 'TransactionController@getOneTransaction']
        );
        $router->post(
            'transaction',
            ['uses' => 'TransactionController@createTransaction']
        );
        $router->post(
            'transaction/{id:\d+}/item',
            ['uses' => 'TransactionController@createTransactionItem']
        );
        $router->delete(
            'transaction/{id}',
            ['uses' => 'TransactionController@deleteTransaction']
        );
        $router->patch(
            'transaction/{id:\d+}/{column:date_time|category|amount}',
            ['uses'=>'TransactionController@editTransaction']
        );
        $router->patch(
            'transaction/{id:\d+}/detail',
            ['uses'=>'TransactionController@editTransactionDetail']
        );
    }
);
