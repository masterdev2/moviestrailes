<?php

namespace App\Manager;

use App\Client\ApiClient;
use App\Builder\ShowBuilder;
use App\Entity\Title;
use Doctrine\ORM\EntityManagerInterface;

class ShowManager
{
    /**
     * @var ApiClient
     */
    protected $client;
    protected $showBuilder;
    protected $tmvdb;
    protected $em;

    /**
     * ReidenceFinder constructor.
     * @param ApiClient $client
     */
    public function __construct(ApiClient $client, ShowBuilder $showBuilder, EntityManagerInterface $em)
    {
        $this->client = $client;
        $this->showBuilder = $showBuilder;
        $this->tmvdb = 'https://api.themoviedb.org/3/find/';
        $this->em = $em;
    }
    
    public function getInfos($imdb, $tmdb)
    {
      $ch = curl_init();
      if($tmdb){
        curl_setopt($ch, CURLOPT_URL, 'https://api.themoviedb.org/3/movie/'.$tmdb.'?api_key=8d6d91941230817f7807d643736e8a49&append_to_response=external_ids');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json')); // Assuming you're requesting JSON
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $data = json_decode($response);
        $imdb = $data->imdb_id;
      }
      if($imdb){
        curl_setopt($ch, CURLOPT_URL, 'https://api.themoviedb.org/3/find/'.$imdb.'?api_key=51b0e2757c81263b10c4999d7d74aeb9&language=en-US&external_source=imdb_id');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json')); // Assuming you're requesting JSON
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $data = json_decode($response);
        if(isset($data->movie_results[0])){
          $tmdb = $data->movie_results[0]->id;
        }
      }
      curl_setopt($ch, CURLOPT_URL, 'https://api.themoviedb.org/3/find/'.$imdb.'?api_key=51b0e2757c81263b10c4999d7d74aeb9&language=en-US&external_source=imdb_id');
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json')); // Assuming you're requesting JSON
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      $response = curl_exec($ch);
      $data = json_decode($response);
      //echo '<pre>';print_r('https://api.themoviedb.org/3/find/'.$imdb.'?api_key=51b0e2757c81263b10c4999d7d74aeb9&language=en-US&external_source=imdb_id');exit;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, 'http://www.omdbapi.com/?i='.$imdb.'&apikey=f36ee63f');
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json')); // Assuming you're requesting JSON
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $response = curl_exec($ch);
      $omdbshow = json_decode($response);

      if(isset($data->Error)){
        return 'error';
      }else{
        if(isset($data->movie_results) && !empty($data->movie_results)){
          $show = $data->movie_results[0];
          $type = 'movie';
        }elseif(isset($data->tv_results) && !empty($data->tv_results)){
          $show = $data->tv_results[0];
          $type = 'serie';
        }else{
          return 'error';
        }

      $show = $this->showBuilder->build($show, $type, $omdbshow);
      $show['tmdb'] = $tmdb;
      $show['imdb'] = $imdb;
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
      }
      return $show;
    }

    public function getTmdbInfos($imdb)
    {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, 'https://api.themoviedb.org/3/find/'.$imdb.'?api_key=51b0e2757c81263b10c4999d7d74aeb9&language=en-US&external_source=imdb_id');
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json')); // Assuming you're requesting JSON
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $response = curl_exec($ch);
      $data = json_decode($response);
      if(isset($data->Error)){
        return 'error';
      }else{
        if($data->movie_results){
          return $data->movie_results;
        }
        if($data->tv_results){
          return $data->tv_results;
        }
      }
      return 'error';
    }

    public function setShowTitle($locale, $newShow){
      if($locale == 'en'){
        $sTitle = $this->getTitle('en' , 'en-US', $newShow);
      }
      if ($locale == 'fr') {
          $sTitle = $this->getTitle('fr', 'fr-FR', $newShow);
      }
      if ($locale == 'es') {
          $sTitle = $this->getTitle('es', 'es-ES', $newShow);
      }
      if ($locale == 'it') {
          $sTitle = $this->getTitle('it', 'it-IT', $newShow);
      }
      if ($locale == 'de') {
          $sTitle = $this->getTitle('de', 'de-DE', $newShow);
      }
      return $sTitle;
    }

    public function getTitle($l , $L, $newShow){
      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, $this->tmvdb.$newShow->getImdb().'?api_key=51b0e2757c81263b10c4999d7d74aeb9&language='.$L.'&external_source=imdb_id');
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json')); // Assuming you're requesting JSON
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $response = curl_exec($ch);
      //dd($newShow);
      $titlefr = $newShow->getTitle();
      if(isset(json_decode($response)->movie_results) && !empty(json_decode($response)->movie_results)){
          $titlefr = json_decode($response)->movie_results[0]->title;
      }
      if(isset(json_decode($response)->tv_results) && !empty(json_decode($response)->tv_results)){
          $titlefr = json_decode($response)->tv_results[0]->name;
      }
      $title = new Title();
      $title->setTitle($titlefr);
      $title->setLang($l);
      $title->setShow($newShow);
      $this->em->persist($title);
      return $title;
  }

}
