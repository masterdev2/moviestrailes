<?php

namespace App\Builder;

use Symfony\Component\HttpFoundation\Request;
use \Datetime;
use App\Entity\Show;

class ShowBuilder
{
    public function __construct()
    {
    }

    /**
     * @param Show[] $show
     * @return array
     */
    public function buildList($shows)
    {
        $dataResponse = array();
        foreach ($shows as $show) {
            $dataResponse[] = $this->build($show);
        }

        return $dataResponse;
    }

    /**
     * @param Show $show
     * @return array
     */
    public function build( $show, $type, $omdbshow)
    {
        //echo "<pre>";print_r($show);exit;
        $totalSeasons = 0;
        if(isset($omdbshow->totalSeasons)){
            $totalSeasons = $omdbshow->totalSeasons;
        }

        if(isset($show->original_name)){
            $title = $show->original_name;
        }
        if(isset($show->original_title)){
            $title = $show->original_title;
        }
        
        $date ="";
        if(isset($show->release_date)){
            $date = $show->release_date;
        }
        if(isset($show->first_air_date)){
            $date = $show->first_air_date;
        }

        return array(
            'title' => $title,
            'id'    => $show->id,
            'year' => $date,
            'poster' => $show->poster_path,
            'type' => $type,
            'totalSeasons' => $totalSeasons,
        );
    }

    public function buildFilmApi( $film, $access_token)
    {
        return array(
            'title' => $film->getTitle(),
            'imdb' => $film->getImdb(),
            'poster' => $film->getPoster(),
            'videoPlayer' => 'https://streamzy.to/public/show/iframe/'.$access_token.'/'.$film->getImdb()
        );
    }

    public function buildFilmsApi( $films, $access_token)
    {
        $dataResponse = array();
        foreach ($films as $film) {
            $dataResponse[] = $this->buildFilmApi($film, $access_token);
        }
        return $dataResponse;
    }

    public function buildSeriesApi( $shows, $access_token)
    {
        $dataResponse = array();
        foreach ($shows as $show) {
            $dataResponse[] = $this->buildSerieApi($show, $access_token);
        }
        return $dataResponse;
    }

    public function buildSerieApi( $show, $access_token)
    {
        $seasons = $this->buildSeasonsApi($show->getSeasns(), $access_token);
        return array(
            'title' => $show->getTitle(),
            'imdb' => $show->getImdb(),
            'poster' => $show->getPoster(),
            'seasons' => $seasons
        );
    }

    public function buildSeasonsApi($seasons, $access_token)
    {
        $dataResponse = array();
        foreach ($seasons as $season) {
            $dataResponse[] = $this->buildSeasonApi($season, $access_token);
        }
        return $dataResponse;
    }

    public function buildSeasonApi($season, $access_token)
    {
        
        return   [ 'title' => $season->getTitle() , 'episodes' => $this->buildEpisodesApi($season->getEpisodes(), $access_token) ];
    }

    public function buildEpisodesApi($episodes, $access_token)
    {
        $dataResponse = array();
        foreach ($episodes as $episode) {
            $dataResponse[] = $this->buildEpisodeApi($episode, $access_token);
        }
        return $dataResponse;
    }

    public function buildEpisodeApi($episode, $access_token)
    {
        return [ 'title' => $episode->getTitle() , 'videoPlayer' => 'https://streamzy.to/public/show/iframe/'.$access_token.'/'.$episode->getImdb()  ];
    }
}
