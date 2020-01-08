<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->group(
    ['prefix'=>'api/','middleware' => 'cors'],
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
        // Create a category
        $router->post(
            'category',
            ['uses' => 'TransactionController@createCategory']
        );
        // Delete transaction
        $router->delete(
            'transaction/{id}',
            ['uses' => 'TransactionController@deleteTransaction']
        );
        // Delete category

        $router->delete(
            'category/{id:\d+}',
            ['uses' => 'TransactionController@deleteCategory']
        );

        // Edit transaction
        $router->patch(
            'transaction/{id:\d+}/{column:date_time|category|amount}',
            ['uses'=>'TransactionController@editTransaction']
        );
        $router->patch(
            'transaction/{id:\d+}/detail',
            ['uses'=>'TransactionController@editTransactionDetail']
        );
        $router->patch(
            'transaction/{id:\d+}/item/{itemId:\d+}/{column:item_name|item_amount|unit_price}',
        //Edit Category
        $router->patch(
            'category/{id:\d+}',
            ['uses' => 'TransactionController@editCategory']
        );
    }
);
