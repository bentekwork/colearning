<?php

namespace PetTrafficKing\StoreBundle\DependencyInjection;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class Stripe
{
    protected $secret;
    protected $endpoint;

    public function __construct($secret, $endpoint)
    {
        $this->secret = $secret;
        $this->endpoint = $endpoint;
    }

    public function processCheckout($price, $card)
    {
        $client = new Client();

        try{
            $request = $client->createRequest('POST', $this->endpoint.'charges',
                array(
                    'auth' => array($this->secret, ' '),
                    'body' => array(
                        'amount' => $price * 100,
                        'currency' => 'usd',
                        'card' => $card
                    )
                )
            );

            $response = $client->send($request)->json();

        } catch (BadResponseException $e){

            $response = array('error', $e->getMessage());
            if($e->getResponse()->getStatusCode() == 402){
                $response = array('error' => 'Your card was declined');
            }
        }

        return $response;

    }

}