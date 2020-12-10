<?php

namespace App\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DependencyInjection\ContainerInterface; // <- Add this
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ApiClient extends Client
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $access;

    private $params;

    /**
     * ApiClient constructor.
     */
    public function __construct(ParameterBagInterface $params, ContainerInterface $container)
    {
        $base_uri = 'http://www.omdbapi.com/';
        $access = 'f36ee63f';
        $this->client = new Client([
            'base_uri' => $base_uri,
            'timeout'  => 200
        ]);
        $this->access = $access;
    }

    /**
     * @param $method
     * @param array $params
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function call($method, array $params = array())
    {
        $params['apikey'] = $this->access;
        $queries = http_build_query($params);
        $method .= $queries ? '?'.$queries : '';


        $response = $this->client->request(
            'GET',
            $method,
            [
                'headers'   =>  array(
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                )
            ]
        );
        echo "<pre>";print_r($this->client);exit;

        //return json_decode($response->getBody())->response;
        return json_decode($response)->response;
    }

    /**
     * @param $method
     * @param array $params
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function callPost($method, array $params = array())
    {
        $params['apikey'] = $this->access;

        $queries = http_build_query($params);
        $method .= $queries ? '?'.$queries : '';

        $response = $this->client->request(
            'POST',
            $method,
            [
                'headers'   =>  array(
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                )
            ]
        );

        return json_decode($response->getBody())->response;
    }
}
