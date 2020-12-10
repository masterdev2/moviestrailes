<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\User;
use App\Entity\Show;
use App\Entity\Statistique;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Builder\ShowBuilder;
use App\Manager\TokenManager;
use App\Manager\ImdbManager;
use App\Manager\StatsManager;

class ApiController extends Controller
{
    protected $em;
    protected $showBuilder;
    protected $tokenManager;
    protected $imdbManager;
	protected $userRepository;
    protected $statsManager;
    public function __construct(EntityManagerInterface $em, ShowBuilder $showBuilder, TokenManager $tokenManager, ImdbManager $imdbManager, StatsManager $statsManager)
    {
        $this->tokenManager = $tokenManager;
        $this->imdbManager = $imdbManager;
        $this->em = $em;
        $this->showBuilder = $showBuilder;
        $this->userRepository = $this->em->getRepository(User::class);
        $this->statsManager = $statsManager;
    }

     /**
     * @Route("/api/movie" , name="apiMovie")
     */
    public function apiMovie(Request $request)
    {
        $response = new Response();
        $accessToken = $request->get("access-token");
        $imdb = $request->get("imdb");

        $test = $this->tokenManager->isValid($accessToken, $response);
        if($test!='true'){
            return $test;
        }

        $test = $this->imdbManager->isValid($imdb, $response);
        if($test!='true'){
            return $test;
        }

        $ShowRepository = $this->em->getRepository(Show::class);
        $show = $ShowRepository->findOneBy( array('imdb' => $imdb) );
        
        $buildedShow = $this->showBuilder->buildFilmApi( $show, $accessToken); 

        $content['showInfos'] = $buildedShow;
        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        $content['data'] = array(
            'type' => 'success'
        );
        $user = $this->userRepository->findOneBy( array('accessToken' => $accessToken) );
        $this->statsManager->incremente($user, 2);
        $response->setContent(json_encode($content));
        return $response;
    }

    /**
     * @Route("/api/movies" , name="apiMovies")
     */
    public function apiMovies(Request $request)
    {
        $response = new Response();
        $accessToken = $request->get("access-token");
        $imdb = $request->get("imdb");
        
        $test = $this->tokenManager->isValid($accessToken, $response);
        if($test!='true'){
            return $test;
        }

        $ShowRepository = $this->em->getRepository(Show::class);
        $shows = $ShowRepository->findBy( array('type' => 'movie') );

        $buildedShow = $this->showBuilder->buildFilmsApi( $shows, $accessToken); 

        $content['showInfos'] = $buildedShow;
        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        $content['data'] = array(
            'type' => 'success'
        );
        $user = $this->userRepository->findOneBy( array('accessToken' => $accessToken) );
        $this->statsManager->incremente($user, 2);
        $response->setContent(json_encode($content));
        return $response;
    }

    /**
     * @Route("/api/anime" , name="apiAnime")
     */
    public function apiAnime(Request $request)
    {
        $response = new Response();
        $accessToken = $request->get("access-token");
        $imdb = $request->get("imdb");
        
        $test = $this->tokenManager->isValid($accessToken, $response);
        if($test!='true'){
            return $test;
        }

        $ShowRepository = $this->em->getRepository(Show::class);
        $show = $ShowRepository->findOneBy( array('imdb' => $imdb) );
        if(!$show){
            $content['data'] = array(
                'type' => 'error'
            );
            $content['error_api'] = "show_not_found";
            $response->setContent(json_encode($content));
            return $response;
        }       
        $buildedShow = $this->showBuilder->buildSerieApi( $show, $accessToken); 

        $content['showInfos'] = $buildedShow;
        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        $content['data'] = array(
            'type' => 'success'
        );
        $user = $this->userRepository->findOneBy( array('accessToken' => $accessToken) );
        $this->statsManager->incremente($user, 2);
        $response->setContent(json_encode($content));
        return $response;
    }

    /**
     * @Route("/api/animes" , name="apiAnimes")
     */
    public function apiAnimes(Request $request)
    {
        $response = new Response();
        $accessToken = $request->get("access-token");
        $imdb = $request->get("imdb");
        
        $test = $this->tokenManager->isValid($accessToken, $response);
        if($test!='true'){
            return $test;
        }

        $ShowRepository = $this->em->getRepository(Show::class);
        $shows = $ShowRepository->findBy( array('type' => 'anime') );

        $buildedShow = $this->showBuilder->buildSeriesApi( $shows, $accessToken); 

        $content['showInfos'] = $buildedShow;
        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        $content['data'] = array(
            'type' => 'success'
        );
        $user = $this->userRepository->findOneBy( array('accessToken' => $accessToken) );
        $this->statsManager->incremente($user, 2);
        $response->setContent(json_encode($content));
        return $response;
    }

    /**
     * @Route("/api/serie" , name="apiSerie")
     */
    public function apiSerie(Request $request)
    {
        $response = new Response();
        $accessToken = $request->get("access-token");
        $imdb = $request->get("imdb");
        
        $test = $this->tokenManager->isValid($accessToken, $response);
        if($test!='true'){
            return $test;
        }

        $ShowRepository = $this->em->getRepository(Show::class);
        $show = $ShowRepository->findOneBy( array('imdb' => $imdb) );
        if(!$show){
            $content['data'] = array(
                'type' => 'error'
            );
            $content['error_api'] = "show_not_found";
            $response->setContent(json_encode($content));
            return $response;
        }       
        $buildedShow = $this->showBuilder->buildSerieApi( $show, $accessToken); 

        $content['showInfos'] = $buildedShow;
        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        $content['data'] = array(
            'type' => 'success'
        );
        $user = $this->userRepository->findOneBy( array('accessToken' => $accessToken) );
        $this->statsManager->incremente($user, 2);
        $response->setContent(json_encode($content));
        return $response;
    }

    /**
     * @Route("/api/series" , name="apiSeries")
     */
    public function apiSeries(Request $request)
    {
        $response = new Response();
        $accessToken = $request->get("access-token");
        $imdb = $request->get("imdb");
        
        $test = $this->tokenManager->isValid($accessToken, $response);
        if($test!='true'){
            return $test;
        }

        $ShowRepository = $this->em->getRepository(Show::class);
        $shows = $ShowRepository->findBy( array('type' => 'series') );

        $buildedShow = $this->showBuilder->buildSeriesApi( $shows, $accessToken); 

        $content['showInfos'] = $buildedShow;
        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        $content['data'] = array(
            'type' => 'success'
        );
        $user = $this->userRepository->findOneBy( array('accessToken' => $accessToken) );
        $this->statsManager->incremente($user, 2);
        $response->setContent(json_encode($content));
        return $response;
    }

    /**
     * @Route("/api/serie_season" , name="apiSeriesSeason")
     */
    public function apiSeriesSeason(Request $request)
    {
        $response = new Response();
        $accessToken = $request->get("access-token");
        $imdb = $request->get("imdb");
        $sea = $request->get("season");
        $epi = $request->get("episode");
        
        $test = $this->tokenManager->isValid($accessToken, $response);
        if($test!='true'){
            return $test;
        }

        $ShowRepository = $this->em->getRepository(Show::class);
        $show = $ShowRepository->findOneBy( array('imdb' => $imdb) );
        if(!$show){
            $content['data'] = array(
                'type' => 'error'
            );
            $content['error_api'] = "show_not_found";
            $response->setContent(json_encode($content));
            return $response;
        }

        foreach ($show->getSeasns() as $key => $season) {
            if( count(explode('Season '.$sea,$season->getTitle()))>1 ){
                if($epi){
                    if(isset($season->getEpisodes()[$epi-1])){
                        $content['showInfos'] = $this->showBuilder->buildEpisodeApi( $season->getEpisodes()[$epi-1], $accessToken);
                        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                        $content['data'] = array(
                            'type' => 'success'
                        );
                        $response->setContent(json_encode($content));
                        return $response;
                    }
                }else{
                    $content['showInfos'] = $this->showBuilder->buildSeasonApi( $season, $accessToken);
                    $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                    $content['data'] = array(
                        'type' => 'success'
                    );
                    $response->setContent(json_encode($content));
                    return $response;
                }
            }
        }

        $buildedShow = $this->showBuilder->buildSerieApi( $show, $accessToken); 

        $content['showInfos'] = $buildedShow;
        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        $content['data'] = array(
            'type' => 'success'
        );
        $user = $this->userRepository->findOneBy( array('accessToken' => $accessToken) );
        $this->statsManager->incremente($user, 2);
        $response->setContent(json_encode($content));
        return $response;
    }

    /**
     * @Route("/api/anime_season" , name="apiAnimesSeason")
     */
    public function apiaAnimesSeason(Request $request)
    {
        $response = new Response();
        $accessToken = $request->get("access-token");
        $imdb = $request->get("imdb");
        $sea = $request->get("season");
        $epi = $request->get("episode");
        
        $test = $this->tokenManager->isValid($accessToken, $response);
        if($test!='true'){
            return $test;
        }

        $ShowRepository = $this->em->getRepository(Show::class);
        $show = $ShowRepository->findOneBy( array('imdb' => $imdb) );
        if(!$show){
            $content['data'] = array(
                'type' => 'error'
            );
            $content['error_api'] = "show_not_found";
            $response->setContent(json_encode($content));
            return $response;
        }

        foreach ($show->getSeasns() as $key => $season) {
            if( count(explode('Season '.$sea,$season->getTitle()))>1 ){
                if($epi){
                    if(isset($season->getEpisodes()[$epi-1])){
                        $content['showInfos'] = $this->showBuilder->buildEpisodeApi( $season->getEpisodes()[$epi-1], $accessToken);
                        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                        $content['data'] = array(
                            'type' => 'success'
                        );
                        $response->setContent(json_encode($content));
                        return $response;
                    }
                }else{
                    $content['showInfos'] = $this->showBuilder->buildSeasonApi( $season, $accessToken);
                    $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                    $content['data'] = array(
                        'type' => 'success'
                    );
                    $response->setContent(json_encode($content));
                    return $response;
                }
            }
        }

        $buildedShow = $this->showBuilder->buildSerieApi( $show, $accessToken); 

        $content['showInfos'] = $buildedShow;
        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        $content['data'] = array(
            'type' => 'success'
        );
        $user = $this->userRepository->findOneBy( array('accessToken' => $accessToken) );
        $this->statsManager->incremente($user, 2);
        $response->setContent(json_encode($content));
        return $response;
    }
}
