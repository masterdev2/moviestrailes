<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Manager\ShowManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Show;
use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Link;

class FilmController extends Controller
{
    protected $showManager;
    protected $em;
    public function __construct(ShowManager $showManager, EntityManagerInterface $em)
    {
        $this->showManager = $showManager;
        $this->em = $em;
    }

    /**
     * @Route("/film/liste" , name="films")
     */
    public function Films(Request $request)
    {
        $showRepository = $this->em->getRepository(Show::class);
        $films = $showRepository->findBy( array('type' => 'movie') );
        return $this->render("Film/index.html.twig", array('films' => $films));
    }

    /**
     * @Route("/film/add" , name="add_film")
     */
    public function addFilm(Request $request)
    {
        if ($request->isMethod('post')) {
            $newShow = new Show();
            $newShow->setTitle($request->request->get('title'));
            $newShow->setImdb($request->request->get('imdb'));
            $newShow->setYear($request->request->get('year'));
            $newShow->setType($request->request->get('type'));
            $newShow->setPoster($request->request->get('poster'));
            $show = $this->showManager->getInfos($request->request->get('imdb'));
            if(isset($show['seasons'])){
                foreach ($show['seasons'] as $key => $seas) {
                    $season = new Season();
                    $season->setTitle($seas['title']);
                    $season->setShow($newShow);
                    foreach ($seas['episodes'] as $key => $ep) {
                        $episode = new Episode();
                        $episode->setTitle($ep['title']);
                        $episode->setImdb($ep['date']);
                        $episode->setYear($ep['imdb']);
                        $episode->setSeason($season);
                        $season->addEpisodes($episode);
                    }
                    $newShow->addSeason($season);
                }
            }
            $this->em->persist($newShow);
            $this->em->flush();
        }
        return $this->render("Film/add.html.twig");
    }

    /**
     * @Route("/film/get_movie_info" , name="get_movie_info")
     */
    public function getMovieInfo(Request $request)
    {
        $imdb = $request->get('imdb');
        $result = $this->showManager->getInfos($imdb);
        return new JsonResponse(array('show' => $result));
    }

    /**
     * @Route("/film/edit/{id}" , name="movie_edit")
     */
    public function editFilm($id, Request $request)
    {
        return $this->render("Film/add.html.twig");
    }
    /**
     * @Route("/film/links/{id}" , name="movie_links")
     */
    public function linksFilm($id, Request $request)
    {
        $showRepository = $this->em->getRepository(Show::class);
        $film = $showRepository->findOneBy( array('id' => $id) );
        if ($request->isMethod('post')) {
            foreach ($request->request as $key => $value) {
                if($key!='links'){
                    if( count(explode('Player',$value)) ==1 ){
                        $link = new Link();
                        $link->setLink($value);
                        $link->setShow($film);
                        $this->em->persist($link);
                        $this->em->flush();
                    }
                }
            }
        }
        return $this->render("Film/links.html.twig", array('film' => $film));
    }
    /**
     * @Route("/film/delete/{id}" , name="movie_delete")
     */
    public function deleteFilm($id, Request $request)
    {
        echo "<pre>";print_r('delete film');
    }
}
