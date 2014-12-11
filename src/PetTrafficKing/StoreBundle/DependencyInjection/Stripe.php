<?php

namespace PetTrafficKing\StoreBundle\DependencyInjection;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class Stripe
{
    protected $endpoint;
    protected $secret;

    public function __construct($secret)
    {
        $this->endpoint = "https://api.stripe.com/v1";
        $this->secret = $secret;

    }

    public function processPayment($amount, $card)
    {
        //new guzzle, send request, get errors handle them
        $client = new Client();
        try {
            $request = $client->createRequest('POST', $this->endpoint.'/charges',
                array(
                    'auth' => array($this->secret, ' '),
                    'body' => array(
                        'amount' => $amount * 100,
                        'currency' => 'usd',
                        'card' => $card
                    )
                )
            );
            $response = $client->send($request);

            return $this->verifyPayment($response->json());

        }
        catch (BadResponseException $e) {
            if($e->getResponse()->getStatusCode() == 402){
                return array('error' => 'Your card was declined deadbeat');
            }

            return array('error' => $e->getMessage());

        }
    }

    public function verifyPayment($charge){
        //check for failure messages
        if(!empty($charge['failure_message'])){

          return array('error', $charge['failure_message']);

        } else {
            return $charge;
        }

    }

}