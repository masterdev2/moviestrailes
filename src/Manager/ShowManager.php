<?php

namespace App\Manager;

use App\Client\ApiClient;
use App\Builder\ShowBuilder;

class ShowManager
{

    /**
     * @var ApiClient
     */
    protected $client;
    protected $showBuilder;


    /**
     * ReidenceFinder constructor.
     * @param ApiClient $client
     */
    public function __construct(ApiClient $client, ShowBuilder $showBuilder)
    {
        $this->client = $client;
        $this->showBuilder = $showBuilder;
    }
    
    public function getInfos($imdb)
    {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, 'http://www.omdbapi.com/?i='.$imdb.'&apikey=f36ee63f');
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json')); // Assuming you're requesting JSON
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $response = curl_exec($ch);
      $data = json_decode($response);
      $show = $this->showBuilder->build($data);
      if(isset($show['totalSeasons']) && $show['totalSeasons']){
        $show['seasons'] = [];
        for ($i=1; $i <= $show['totalSeasons'] ; $i++) { 
          curl_setopt($ch, CURLOPT_URL, 'http://www.omdbapi.com/?i='.$imdb.'&apikey=f36ee63f&Season='.$i);
          curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json')); // Assuming you're requesting JSON
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          $response = curl_exec($ch);
          $data = json_decode($response);
            if(isset($data->Title)){
              $show['seasons']['season'.$i] = ['title' => $data->Title .' Season '.$data->Season ];
              if(isset($data->Episodes)){
                $show['seasons']['season'.$i]['count'] =  count($data->Episodes);
                foreach ($data->Episodes as $key => $ep) {
                  $show['seasons']['season'.$i]['episodes'][$ep->Episode] = ['title' => $ep->Title , 'date' => $ep->Released, 'imdb' => $ep->imdbID ];
                }
              }
            }
        }
      }
      return $show;
    }
}
