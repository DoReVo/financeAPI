<?php

use GuzzleHttp\Client;

class InsertTransaction extends TestCase
{

    // use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */

    private $http;

    public function testExample()
    {

        $this->http = new Client(
            [
                'base_uri' => 'http://localhost/api/',
            ]
        );

        $data = array(
            'date_time' => '2020-04-13',
            'amount' => '30',
            'category' => '1',
            'detail' => 'Test detail',
        );

        // $result = $this->http->request('GET', 'transaction');

        $response = $this->http->post('transaction', ['query' => $data]);

        $this->assertEquals(
            200,
            $response->getStatusCode()
        );

        $body = $response->getBody();
        echo ' \n hello \n WHAT';
        // print($body);
        // $body = json_decode($response->getBody());
        // // $body = $response->getBody()->getContents()
        // foreach ($body as $key => $value) {
        //     foreach ($body[$key] as $bodyKey => $bodyValue) {
        //         echo $bodyValue;
        //     }

        // }
        // foreach ($body as $key => $value) {
        //     // print $body[$key];
        //     // print_r( $value);
        //     echo($body);
        // }

        // $this->seeInDatabase('finance_transaction', ['id' => 5]);
    }
}
