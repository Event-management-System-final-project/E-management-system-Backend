<?php
namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ChapaService
{
    protected $client;
    protected $baseUrl;
    protected $secretkey;


    public function __construct()
    {
        $this->client = new Client();
        $this->baseUrl = config('Chapa.base_url');
        $this->secretkey = config('Chapa.secret_key');
   
    }

    public function initializePayment($data){
 
        try{
            $response = $this->client->post($this->baseUrl . "transaction/initialize", [
                'headers' =>[
                    'Authorization' =>'Bearer ' .  $this->secretkey,
                    'Content-Type' => 'application/json'
                ],
                'json' => $data
            ]);

            return json_decode($response->getBody(), true);

        }catch (RequestException $e) {
            if ($e->hasResponse()) {
                return json_decode($e->getResponse()->getBody()->getContents(), true);
            }

            return ['error' => 'Something went wrooooooooong'];
        }
    }






    public function verifyPayment($reference){
        try{
            $response = $this->client->get($this->baseUrl."transaction/verify/".$reference, [
                'headers' =>[
                    'Authorization' => 'Bearer ' . $this->secretkey,
                    'Content-Type' => 'application/json'
                ]
            ]);

            return json_decode($response->getBody(), true);

        }catch (RequestException $e) {
            if ($e->hasResponse()) {
                return json_decode($e->getResponse()->getBody()->getContents(), true);
            }

            return ['error' => 'Something went wrooooooooong'];
        }
    }


    
}
