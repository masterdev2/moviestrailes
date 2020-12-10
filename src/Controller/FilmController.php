<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Manager\ShowManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Show;
use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Link;
use App\Entity\Title;
use Knp\Component\Pager\PaginatorInterface;

class FilmController extends Controller
{
    protected $showManager;
    protected $em;
    protected $paginator;
    protected $tmvdb;
    public function __construct(ShowManager $showManager, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->showManager = $showManager;
        $this->em = $em;
        $this->paginator = $paginator;
        $this->tmvdb = 'https://api.themoviedb.org/3/find/';
    }
	
	/**
    * @Route(
     *     "/websites/all" , name = "websitesAll" ,
     * )
     */
    public function websitesAll(Request $request)
    {
        $query = 'SELECT * FROM `websites` WHERE 1';
        $statement = $this->em->getConnection()->prepare($query);
        $statement->execute();
        $websites = $statement->fetchAll();
		$data = [];
		foreach($websites as $website){
			$cDate = date("Y-m-d");
			$sqlS = 'SELECT * FROM `websites_stats` WHERE `site_id`='.$website['id'].' and `date`="'.$cDate.'"';
			$statement = $this->em->getConnection()->prepare($sqlS);
			$statement->execute();
			$stats = $statement->fetchAll();
			if(count($stats) == 0){
				$qb = $this->em->createQueryBuilder();
				$qb->select('t')
				   ->from('App:Title', 't')
				   ->where('t.lang = ?1')
				   ->orderBy('t.id', 'DESC')
				   ->setParameter(1, $website['lang']);
				$query = $qb->getQuery();
				$array = $query->getResult();
				shuffle($array);
				
				for($i=1;$i<=$website['max'];$i++){
					if(isset($array[$i])){
						$show = $array[$i]->getShow();
						array_push($data, $show->getImdb().'|'.$show->getTmdb().'|'.$show->getTitle().'|'.$show->getYear());
					}
				}
				//dd($data);
				if(count($data) >= 1){
					$ch = curl_init($website['link'].'insertfilm.php');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
					$response = curl_exec($ch);
					curl_close($ch);
				}
				$sqlI = 'INSERT INTO `websites_stats`(`site_id`, `date`, `count`) VALUES ("'.$website['id'].'","'.$cDate.'",'.count($data).')';
				$statement = $this->em->getConnection()->prepare($sqlI);
				$statement->execute();
			}
		}
		return new JsonResponse($data);
    }
	
	/**
    * @Route(
     *     "/film/simple-liste-json" , name = "filmsSimplejson" ,
     * )
     */
    public function FilmsListeSimpleJson(Request $request)
    {
        if(!$_GET || !$_GET['url']){
            return new JsonResponse([]);
        }
        $query = 'SELECT * FROM `websites` WHERE `link`="'.$_GET['url'].'" ORDER BY `websites`.`id` DESC';
        $statement = $this->em->getConnection()->prepare($query);
        $statement->execute();
        $website = $statement->fetchAll();
        if(count($website) < 1){
            return new JsonResponse([]);
        }
        $website = $website[0];
        $qb = $this->em->createQueryBuilder();
        $qb->select('t')
           ->from('App:Title', 't')
           ->where('t.lang = ?1')
           ->orderBy('t.id', 'DESC')
           ->setParameter(1, $website['lang']);
        $query = $qb->getQuery();

        $array = $query->getResult();
        shuffle($array);
		$data = [];
        for($i=1;$i<=$website['max'];$i++){
			if(isset($array[$i])){
            	$show = $array[$i]->getShow();
				array_push($data, $show->getImdb().'|'.$show->getTmdb().'|'.$show->getTitle().'|'.$show->getYear());
			}
        }
		return new JsonResponse($data);
    }
	
	/**
    * @Route(
     *     "/{_locale}/film/simple-liste" , name = "filmsSimple" ,
     * )
     */
    public function FilmsListeSimple(Request $request)
    {
        $page = isset($_GET['page']) ?$_GET['page'] : 1;
        $locale = $request->getLocale();
        $qb = $this->em->createQueryBuilder();
        $locale = $request->getLocale();
        $qb->select('t')
           ->from('App:Title', 't')
           ->where('t.lang = ?1')
           ->orderBy('t.id', 'DESC')
           ->setParameter(1, $locale);
        $query = $qb->getQuery();

        $array = $query->getResult();
        //dd(count($array));
        foreach($array as $t){
            $show = $t->getShow();
            echo $show->getImdb().'|'.$show->getTmdb().'|'.$show->getTitle().'|'.$show->getYear().'<br>';
        }
        exit;
        return $this->render("simpleListe.html.twig", array('titles' => $pagination));
    }
	
    /**
    * @Route(
     *     "/{_locale}/film/liste" , name = "films" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function Films(Request $request)
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

        return $this->render("Film/index.html.twig", array('films' => $pagination));
    }

    /**
    * @Route(
     *     "/{_locale}/film/add" , name = "add_film" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function addFilm(Request $request)
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('user_login');
        }
        if ($request->isMethod('post')) {

            $show = $this->showManager->getInfos($request->request->get('imdb'), null);

            $newShow = new Show();
            $newShow->setId($request->request->get('imdb'));
            $newShow->setTmdb($show['id']);
            $newShow->setTitle($show['title']);
            $newShow->setYear($show['year']);
            $newShow->setPoster('http://image.tmdb.org/t/p/w500'.$show['poster']);
            $newShow->setImdb($request->request->get('imdb'));
            $newShow->setType($request->request->get('type'));

            /* $show = $this->showManager->getInfos($request->request->get('imdb'));
            if(isset($show['seasons'])){
                foreach ($show['seasons'] as $key => $seas) {
                    $season = new Season();
                    $season->setTitle($seas['title']);
                    $season->setShow($newShow);
                    foreach ($seas['episodes'] as $key => $ep) {
                        $episode = new Episode();
                        $episode->setId($ep['imdb']);
                        $episode->setTitle($ep['title']);
                        $episode->setImdb($ep['imdb']);
                        $episode->setYear($ep['date']);
                        $episode->setSeason($season);
                        $season->addEpisodes($episode);
                    }
                    $newShow->addSeason($season);
                }
            }
            $this->em->persist($newShow);
            $this->em->flush();*/

            $locale = $request->getLocale();

            $sTitle = $this->showManager->setShowTitle($locale, $newShow);
            $newShow->addTitle($sTitle);
            $this->em->flush();

            foreach ($request->request as $key => $value) {
                if( $key!='links' && ( count(explode('titre',$key)) >1 || count(explode('url',$key))>1  ) ){
                    if( count(explode('Player',$value)) ==1 ){
                        $link = new Link();
                        $ls = explode('?v=',$value);
                        if(count($ls)>=2){
                            $newLink = 'https://www.youtube.com/embed/'.$ls[1];
                            $link->setLink($newLink);
                        }else{
                            $link->setLink($value);
                        }
                        if(!$show){
                            $link->setEpisode($newShow);
                        }else{
                            $link->setShow($newShow);
                        }
                        $link->setLang($request->request->get('langue'));
                        $this->em->persist($link);
                        $this->em->flush();

                        $newShow->addLink($link);
                        $this->em->merge($newShow);
                        $this->em->flush();
                    }
                }
            }

            if($request->request->get('type')=='movie'){
                return $this->redirectToRoute('films');
            }elseif($request->request->get('type')=='series'){
                return $this->redirectToRoute('series');
            }else{
                return $this->redirectToRoute('animes');
            }
        }
        $this->addFlash('success', 'Show ajouté avec succés');
        return $this->render("Film/add.html.twig");
    }

    /**
     * @Route("/film/get_movie_info" , name="get_movie_info")
     */
    public function getMovieInfo(Request $request)
    {
        $imdb = $request->get('imdb');
        $tmdb = $request->get('tmdb');

        $showRepository = $this->em->getRepository(Show::class);
        $result = $this->showManager->getInfos($imdb, $tmdb);
        $show = $showRepository->findOneBy( array('imdb' => $result['imdb']) );
        if($show){
            //return new JsonResponse(array('show' => 'Show exist'));
        }

        return new JsonResponse(array('show' => $result));
    }

    /**
    * @Route(
     *     "/{_locale}/show/links/{imdb}" , name = "movie_links" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function linksFilm($imdb, Request $request)
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('user_login');
        }
        
        $showRepository = $this->em->getRepository(Show::class);
        $film = $showRepository->findOneBy( array('imdb' => $imdb) );
        $show = $film;
        $episodeRepository = $this->em->getRepository(Episode::class);
        $episode = $episodeRepository->findOneBy( array('imdb' => $imdb) );

        if(!$film){
            $film = $episode;
        }

        if ($request->isMethod('post')) {
            foreach ($film->getLinks() as $link) {
                $this->em->remove($link);
                $this->em->flush();
            }
            foreach ($request->request as $key => $value) {
                if($key!='links' && $key!='langue'){
                    if( count(explode('Player',$value)) ==1 ){
						
                        $link = new Link();
                        $ls = explode('?v=',$value);
                        if(count($ls)>=2){
                            $newLink = 'https://www.youtube.com/embed/'.$ls[1];
                            $link->setLink($newLink);
                        }else{
                            $link->setLink($value);
                        }
						$link->setLang($request->request->get('langue'));
                        if(!$show){
                            $link->setEpisode($film);
                        }else{
                            $link->setShow($film);
                        }
                        $this->em->persist($link);
                        $this->em->flush();

                        $film->addLink($link);
                        $this->em->merge($film);
                        $this->em->flush();
                    }
                }
            }
        }
        return $this->render("Film/links.html.twig", array('film' => $film));
    }

    /**
    * @Route(
     *     "/{_locale}/film/delete/{id}" , name = "movie_delete" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function deleteFilm($id, Request $request)
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('user_login');
        }
        $showRepository = $this->em->getRepository(Show::class);
        $show = $showRepository->findOneBy( array('id' => $id) );

        foreach ($show->getLinks() as $link) {
            $this->em->remove($link);
            $this->em->flush();
        }

        foreach ($show->getTitles() as $title) {
            $this->em->remove($title);
            $this->em->flush();
        }
        $this->em->remove($show);
        $this->em->flush();
        return $this->redirectToRoute('films');
    }

    /**
    * @Route(
     *     "/{_locale}/show/seasons/{id}" , name = "show_seasons" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function showSeasons($id, Request $request)
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('user_login');
        }
        $showRepository = $this->em->getRepository(Show::class);
        $show = $showRepository->findOneBy( array('id' => $id) );
        $seasons = $show->getSeasns();
        return $this->render("seasons.html.twig", array('seasons' => $seasons));
    }

    /**
    * @Route(
     *     "/{_locale}/season/episodes/{id}" , name = "season_eps" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function seasonEps($id, Request $request)
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('user_login');
        }
        $showRepository = $this->em->getRepository(Season::class);
        $show = $showRepository->findOneBy( array('id' => $id) );
        $episodes = $show->getEpisodes();
        return $this->render("episodes.html.twig", array('episodes' => $episodes));
    }

    /**
    * @Route(
     *     "/{_locale}/film/search" , name = "search_films" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function searchFilms(Request $request)
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('user_login');
        }

        $locale = $request->getLocale();
        $query = $request->request->get('query');
        $year = $request->request->get('Year');

        $qb = $this->em->createQueryBuilder();
        $qb->select('s')
           ->from('App:Show', 's')
           ->where('s.type = :type')
           ->setParameter('type', "movie");
        if($query!=''){
            $qb->leftJoin('App:Title', 't', 'WITH', 't.show = s')
              ->andwhere('t.lang = :lang');
            $qb->andWhere($qb->expr()->andX(
                $qb->expr()->orX(
                    $qb->expr()->andX("t.title like :word"),
                    $qb->expr()->andX("s.id like :word")
                )
            ));
            $qb->setParameter('lang', $locale)
               ->setParameter('word', '%'.$query.'%');
        }
        if($year!=''){
            $qb->andwhere('s.year like :year')
            ->setParameter('year', '%'.$year.'%');
        }
        
        $qb->orderBy('s.id', 'DESC');
        $pagination = $this->paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            50/*limit per page*/
        );

        return $this->render("Film/index.html.twig", array('films' => $pagination));
    }

    /**
    * @Route(
     *     "/lang/switch/{lang}" , name = "lang_switch" ,
     * )
     */
    public function langSwitch($lang, Request $request)
    {
        $request->setLocale($lang);
        $referer = $request->headers->get('referer');
        if(count(explode('en', $referer))>1){
            $referer = str_replace('/en/', '/'.$lang.'/', $referer);
        } 
        if(count(explode('fr', $referer))>1){
            $referer = str_replace('/fr/', '/'.$lang.'/', $referer);
        }
        if(count(explode('it', $referer))>1){
            $referer = str_replace('/it/', '/'.$lang.'/', $referer);
        }
        if(count(explode('es', $referer))>1){
            $referer = str_replace('/es/', '/'.$lang.'/', $referer);
        }
        if(count(explode('de', $referer))>1){
            $referer = str_replace('/de/', '/'.$lang.'/', $referer);
        }
        return new RedirectResponse($referer);
    }

    /**
    * @Route(
     *     "/{_locale}/film/noVid" , name = "film_with_no_videos" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function filmNoVideos(Request $request)
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('user_login');
        }
        $sql = 'SELECT `show_id` FROM `links` WHERE 1';

        $statement = $this->em->getConnection()->prepare($sql);
        $statement->execute();

        $results = $statement->fetchAll();
        $ids = [];
        foreach ($results as $key => $result) {
           $id = $result['show_id'];
           if(!in_array($id, $ids)){
            array_push($ids, $id);
           }
        }

        $query = $request->request->get('query');
        $qb = $this->em->createQueryBuilder();
        $qb->select('s')
           ->from('App:Show', 's')
           ->where('s.type = ?1')
           ->andWhere('s.id NOT IN (:ids)')
           ->orderBy('s.id', 'DESC');
        $qb->setParameter(1, 'movie')
           ->setParameter('ids', $ids);
        $query = $qb->getQuery();

        $pagination = $this->paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            50/*limit per page*/
        );  
        //dd($pagination[0]);
        return $this->render("Film/index.html.twig", array('films' => $pagination));
    }

    /**
     * @Route("/show/import/{min}/{max}" , name="show_import")
     */
    public function showImport($min, $max, Request $request)
    {
        $handle = fopen('Streamzy-List.csv','r');
        $data = fgetcsv($handle);
        $datats = array();
        while ( ($data = fgetcsv($handle) ) !== FALSE ) {
            array_push($datats, $data);
        }
        //echo "<pre>";print_r(count($datats));exit;
        $j= $min;
        $k= 0;
        for ($i=$min; $i < $max ; $i++) {
            $k++;
            $data =$datats[$i]; 
            $imdb = $data[0];
            
            $showRepository = $this->em->getRepository(Show::class);
            $show = $showRepository->findOneBy( array('imdb' => $imdb) );
            if(!$show){
                $show = $this->showManager->getInfos($imdb, null);
                if($show!='error'){
                    $j++;
                    $newShow = new Show();
                    $newShow->setId($imdb);
                    $newShow->setImdb($imdb);
                    $newShow->setTmdb($show['id']);
                    $newShow->setTitle($show['title']);

                    $newShow->setYear($show['year']);
                    $newShow->setType($show['type']);
                    $newShow->setPoster('http://image.tmdb.org/t/p/w500'.$show['poster']);
                    /*if(isset($show['seasons'])){
                        foreach ($show['seasons'] as $key => $seas) {
                            $season = new Season();
                            $season->setTitle($seas['title']);
                            $season->setShow($newShow);
                            foreach ($seas['episodes'] as $key => $ep) {
                                $episode = new Episode();
                                $episode->setId($ep['imdb']);
                                $episode->setTitle($ep['title']);
                                $episode->setImdb($ep['imdb']);
                                $episode->setYear($ep['date']);
                                $episode->setSeason($season);
                                $season->addEpisodes($episode);
                            }
                            $newShow->addSeason($season);
                        }
                    }*/
                    if(!isset($show['seasons'])){
                        $this->em->persist($newShow);
                        $this->em->flush();
                    }
                    $frTitle = $this->getTitle('fr', 'fr-FR', $newShow);
                    if($frTitle){
                        $newShow->addTitle($frTitle);
                        $this->em->flush();
                    }
                }
            }
        }
        exit('done');
        exit('k= '.$k.' #here '.$j);
    }

    /**
     * @Route("/show/imports/links/{offset}" , name="show_import_links")
     */
    public function showImportLinks($offset, Request $request)
    {
        $handle = fopen('Streamzy-List.csv','r');
        $data = fgetcsv($handle);
        $datats = array();
        while ( ($data = fgetcsv($handle) ) !== FALSE ) {
            $datats[$data[0]] = $data;
        }
        $linkRepository = $this->em->getRepository(Link::class);
        $k= 0;

        $qb = $this->em->createQueryBuilder();
        $qb->select('s')
           ->from('App:Show', 's')
           ->where('s.type = :type')
           ->setParameter('type', 'movie')
           ->orderBy('s.id', 'DESC')
           ->setFirstResult( $offset )
           ->setMaxResults( 2000 );
        $query = $qb->getQuery();
        $shows = $query->getResult();
        foreach ($shows as $key => $show) {
            $linkchars = [];
            if(isset($datats[$show->getId()])){
                $linkchars[] = $datats[$show->getId()][1];
                //$linkchars[] = $datats[$show->getId()][2];
                //$linkchars[] = $datats[$show->getId()][3];
                //echo '<pre>';print_r($linkchars);exit;
                foreach ($linkchars as $key => $linkchar) {
                    if($linkchar){
                        $existLink = $linkRepository->findOneBy( array('link' => $linkchar) );
                        if(!$existLink){
                            $link = new Link();
                            $link->setLink($linkchar);
                            $link->setShow($show);
                            $link->setLang('en');
                            $this->em->persist($link);
                            $this->em->flush();
                            $show->addLink($link);
                            $this->em->merge($show);
                            $this->em->flush();
                        }
                    }
                }
            }
        }
        exit('done');
    }

    /**
     * @Route("/show/titles/{offset}" , name="show_titles")
     */
    public function showTitles($offset, Request $request)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('s')
           ->from('App:Show', 's')
           ->where('s.type = :type')
           ->setParameter('type', 'movie')
           ->setFirstResult( $offset )
           ->orderBy('s.id', 'DESC')
           ->setMaxResults( 1000 );
       $query = $qb->getQuery();
       $shows = $query->getResult();

       $i = 0;
        foreach ($shows as $key => $show) {
            //echo '<pre>';print_r(count($show->getTitles()));exit;
            if(!count($show->getTitles())){
                $i++;
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $this->tmvdb.$show->getImdb().'?api_key=51b0e2757c81263b10c4999d7d74aeb9&language=en-US&external_source=imdb_id');
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json')); // Assuming you're requesting JSON
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);


                if(isset(json_decode($response)->movie_results) && !empty(json_decode($response)->movie_results)){
                    $titlefr = json_decode($response)->movie_results[0]->title;
                    $title = new Title();
                    $title->setTitle($titlefr);
                    $title->setLang('en');
                    $title->setShow($show);
                    $this->em->persist($title);
                    $show->addTitle($title);
                    $this->em->merge($show);
                }
                $this->em->flush();

                curl_setopt($ch, CURLOPT_URL, $this->tmvdb.$show->getImdb().'?api_key=51b0e2757c81263b10c4999d7d74aeb9&language=fr-FR&external_source=imdb_id');
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json')); // Assuming you're requesting JSON
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);


                if(isset(json_decode($response)->movie_results) && !empty(json_decode($response)->movie_results)){
                    $titlefr = json_decode($response)->movie_results[0]->title;
                    $title = new Title();
                    $title->setTitle($titlefr);
                    $title->setLang('fr');
                    $title->setShow($show);
                    $this->em->persist($title);
                    $show->addTitle($title);
                    $this->em->merge($show);
                }
                $this->em->flush();

                curl_setopt($ch, CURLOPT_URL, $this->tmvdb.$show->getImdb().'?api_key=51b0e2757c81263b10c4999d7d74aeb9&language=es-ES&external_source=imdb_id');
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json')); // Assuming you're requesting JSON
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);

                if(isset(json_decode($response)->movie_results) && !empty(json_decode($response)->movie_results)){
                    $titlees = json_decode($response)->movie_results[0]->title;
                    $title = new Title();
                    $title->setTitle($titlees);
                    $title->setLang('es');
                    $title->setShow($show);
                    $this->em->persist($title);
                    $show->addTitle($title);
                    $this->em->merge($show);
                }
                $this->em->flush();

                curl_setopt($ch, CURLOPT_URL, $this->tmvdb.$show->getImdb().'?api_key=51b0e2757c81263b10c4999d7d74aeb9&language=it-IT&external_source=imdb_id');
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json')); // Assuming you're requesting JSON
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);

                if(isset(json_decode($response)->movie_results) && !empty(json_decode($response)->movie_results)){
                    $titleit = json_decode($response)->movie_results[0]->title;
                    $title = new Title();
                    $title->setTitle($titleit);
                    $title->setLang('it');
                    $title->setShow($show);
                    $this->em->persist($title);
                    $show->addTitle($title);
                    $this->em->merge($show);
                }
                $this->em->flush();

                curl_setopt($ch, CURLOPT_URL, $this->tmvdb.$show->getImdb().'?api_key=51b0e2757c81263b10c4999d7d74aeb9&language=de-DE&external_source=imdb_id');
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json')); // Assuming you're requesting JSON
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);

                if(isset(json_decode($response)->movie_results) && !empty(json_decode($response)->movie_results)){
                    $titlede = json_decode($response)->movie_results[0]->title;
                    $title = new Title();
                    $title->setTitle($titlede);
                    $title->setLang('de');
                    $title->setShow($show);
                    $this->em->persist($title);
                    $show->addTitle($title);
                    $this->em->merge($show);
                }
                $this->em->flush();
                /*$slugFr = $this->slugify($titlefr);

                $link = 'https://wwv.filmgratuit.net/films/'.$slugFr.'.html';
                $headers = @get_headers($link);
                if($headers[0]!='HTTP/1.1 404 Not Found'){
                    $i++;
                    $homepage = file_get_contents($link);
                    if(isset( explode('<iframe',$homepage)[1] )){
                    $linkchar = explode('" scro',explode('data-src="',explode('/iframe>',explode('<iframe',$homepage)[1])[0])[1])[0];
                    echo "<pre>";print_r( $linkchar );

                        $link = new Link();
                        $link->setLink($linkchar);
                        $link->setShow($show);
                        $link->setLang('fr');

                        $this->em->persist($link);
                        $this->em->flush();

                        $show->addLink($link);
                        $this->em->merge($show);
                        $this->em->flush();
                    }
                    //exit;
                }*/
                
            }
        }
        exit('re'.$i.'er');
    }

    public static function slugify($text)
    {
      $text = preg_replace('~[^\pL\d]+~u', '-', $text);

      // transliterate
      $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

      // remove unwanted characters
      $text = preg_replace('~[^-\w]+~', '', $text);

      // trim
      $text = trim($text, '-');

      // remove duplicate -
      $text = preg_replace('~-+~', '-', $text);

      // lowercase
      $text = strtolower($text);

      if (empty($text)) {
        return 'n-a';
      }

      return $text;
    }

    /**
     * @Route("/link/get_link_src" , name="get_link_src")
     */
    public function getLinkSrc(Request $request)
    {
        $id = $request->get('id');

        $linkRepository = $this->em->getRepository(Link::class);
        $link = $linkRepository->findOneBy( array('id' => $id) );
        if(!$link){
            return new JsonResponse(array('link' => "Link doesn't exist"));
        }

        return new JsonResponse(array('link' => $link->getLink()));
    }

    /**
     * @Route("/show/update/titles/{min}/{max}" , name="show_update_titles")
     */
    public function show_update_titles($min, $max, Request $request)
    {
        exit;
        $handle = fopen('USA_DATA.csv','r');
        $data = fgetcsv($handle);
        $datats = array();
        while ( ($data = fgetcsv($handle) ) !== FALSE ) {
            $datats[] = $data;
        }
        $showRepository = $this->em->getRepository(Show::class);
        for ($i=$min; $i <= $max ; $i++) { 
            /*$sql = 'DELETE FROM `titles` WHERE `show_id`="'.$datats[$i][0].'" AND `lang`!="en"';
            $statement = $this->em->getConnection()->prepare($sql);
            $statement->execute();*/
            //echo '<pre>';print_r($sql);
            $film = $showRepository->findOneBy( array('imdb' => $datats[$i][0]) );
            if($film){
                $title = new Title();
                $title->setTitle($film->getTitle());
                $title->setLang('en');
                $title->setShow($film);
                $this->em->persist($title);
                $film->addTitle($title);
                $this->em->merge($film);
                $this->em->flush();
            }
        }
        exit('re'.count($datats).'er');
    }
}
