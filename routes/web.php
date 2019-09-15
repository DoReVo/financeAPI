<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

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
        $router->delete(
            'transaction/{id}',
            ['uses' => 'TransactionController@deleteTransaction']
        );
        $router->put(
            'transaction/{id}',
            ['uses' => 'TransactionController@replaceTransaction']
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
