<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(
    ['prefix' => 'api/', 'middleware' => 'verify', 'cors'],
    function () use ($router) {
        // Get all transaction
        $router->get(
            'transaction',
            ['uses' => 'TransactionController@getAllTransaction']
        );
        // Get 1 transaction
        $router->get(
            'transaction/{id}',
            ['uses' => 'TransactionController@getOneTransaction']
        );
        // Create a transaction
        $router->post(
            'transaction',
            ['uses' => 'TransactionController@createTransaction']
        );
        // Create a transaction's item
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
            'transaction/{id:\d+}',
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
            ['uses' => 'TransactionController@editTransaction']
        );
        // Edit transaction detail
        $router->patch(
            'transaction/{id:\d+}/detail',
            ['uses' => 'TransactionController@editTransactionDetail']
        );
        // Edit transaction Item
        $router->patch(
            'transaction/{id:\d+}/item/{itemId:\d+}/{column:item_name|item_amount|unit_price}',
            ['uses' => 'TransactionController@editTransactionItem']
        );
        //Edit Category
        $router->patch(
            'category/{id:\d+}',
            ['uses' => 'TransactionController@editCategory']
        );
    }
);
