<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Show;
use App\Entity\Season;
use App\Entity\Episode;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Manager\ShowManager;

class AnimeController extends Controller
{
    protected $em;
    protected $paginator;
    public function __construct(ShowManager $showManager, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->em = $em;
        $this->paginator = $paginator;
        $this->showManager = $showManager;
    }

    /**
    * @Route(
     *     "/{_locale}/anime/liste" , name = "animes" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function Animes(Request $request)
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('user_login');
        }
        $locale = $request->getLocale();
        $qb = $this->em->createQueryBuilder();
        $qb->select('t')
           ->from('App:Title', 't')
           ->where('t.lang = ?1')
           ->orderBy('t.id', 'DESC');
        $qb->setParameter(1, $locale);
        $query = $qb->getQuery();

        $pagination = $this->paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            50/*limit per page*/
        );

        return $this->render("Anime/index.html.twig", array('animes' => $pagination));
    }

    /**
    * @Route(
     *     "/{_locale}/anime/add" , name = "add_anime" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function addAnime(Request $request)
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('user_login');
        }
        return $this->render("Anime/add.html.twig");
    }

    /**
    * @Route(
     *     "/{_locale}/anime/search" , name = "search_animes" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function searchAnimes(Request $request)
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('user_login');
        }
        
        $locale = $request->getLocale();
        $query = $request->request->get('query' );
        $sql = 'SELECT id FROM `titles` WHERE `show_id` IN (SELECT id FROM `shows` WHERE `type`="anime") AND (`show_id` = "'.$query.'" OR `title` LIKE "%'.$query.'%")';

        $conn = $this->em->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $ids = $stmt->fetchAll();
        $ides = '';
        foreach ($ids as $key => $id) {
            if($ides){
                $ides .=',';
            }
            $ides .=$id['id'];
        }        

        $qb = $this->em->createQueryBuilder();
        $qb->select('t')
           ->from('App:Title', 't');
        if($query!=''){
           $qb->add('where', $qb->expr()->in('t.id', ':ides'))
           ->setParameter('ides', $ides);
        }

        $qb->andwhere('t.lang = ?1')
           ->setParameter(1, $locale);
        
        $qb->orderBy('t.id', 'DESC');
        
        $query = $qb->getQuery();

        $pagination = $this->paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            50/*limit per page*/
        );
        return $this->render("Anime/index.html.twig", array('animes' => $pagination));
    }

    /**
    * @Route(
     *     "/{_locale}/anime/delete/{id}" , name = "anime_delete" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function deleteAnime($id, Request $request)
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('user_login');
        }
        $showRepository = $this->em->getRepository(Show::class);
        $show = $showRepository->findOneBy( array('id' => $id) );

        foreach ($show->getSeasns() as $season) {
            foreach ($season->getEpisodes() as $key => $episode) {
                foreach ($episode->getLinks() as $link) {
                    $this->em->remove($link);
                    $this->em->flush();
                }
                $this->em->remove($episode);
                $this->em->flush();
            }
            $this->em->remove($season);
            $this->em->flush();
        }

        foreach ($show->getTitles() as $title) {
            $this->em->remove($title);
            $this->em->flush();
        }

        //$this->em->remove($show);
        $this->em->flush();
        return $this->redirectToRoute('animes');
    }

    /**
    * @Route(
     *     "/{_locale}/anime/update/{id}" , name = "anime_update" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function animeUpdate($id, Request $request)
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('user_login');
        }

        $showRepository = $this->em->getRepository(Show::class);
        $show = $showRepository->findOneBy( array('id' => $id) );
        $seasns = $show->getSeasns();
        $newSns = array();
        $newEps = array();
        foreach ($seasns as $key => $seasn) {
            $newSns[$seasn->getTitle()] = $seasn;
            foreach ($seasn->getEpisodes() as $key => $ep) {
                $newEps[$seasn->getTitle()][$ep->getTitle()] = $ep;
            }
        }

        $showInfo = $this->showManager->getInfos($id);
        //echo "<pre>";print_r($showInfo);exit;
        if(isset($showInfo['seasons'])){
            foreach ($showInfo['seasons'] as $key => $seas) {
                if(!isset($newSns[$seas['title']])){
                    $season = new Season();
                    $season->setTitle($seas['title']);
                    $season->setShow($show);
                    $show->addSeason($season);
                    $this->em->persist($season);
                }

                foreach ($seas['episodes'] as $key => $ep) {
                    if( !isset($newEps[$seas['title']][$ep['title']]) ){
                        $episode = new Episode();
                        $episode->setId($ep['imdb']);
                        $episode->setTitle($ep['title']);
                        $episode->setImdb($ep['imdb']);
                        $episode->setYear($ep['date']);
                        $episode->setSeason($season);
                        $season->addEpisodes($episode);
                        $this->em->persist($episode);
                        $this->em->merge($season);
                    }
                }
            }
        }
        $this->em->merge($show);
        $this->em->flush();
        $this->addFlash('success', 'Anime Modifié avec succés');
        return $this->redirectToRoute('animes');
    }
}
