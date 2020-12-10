<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Show;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

class SerieController extends Controller
{
    protected $em;
    protected $paginator;
    public function __construct(EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->em = $em;
        $this->paginator = $paginator;
    }

    /**
    * @Route(
     *     "/{_locale}/serie/liste" , name = "series" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function Series(Request $request)
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
        return $this->render("Serie/index.html.twig", array('series' => $pagination));
    }

    /**
    * @Route(
     *     "/{_locale}/serie/add" , name = "add_serie" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function addSerie(Request $request)
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('user_login');
        }
        return $this->render("Serie/add.html.twig");
    }

    /**
    * @Route(
     *     "/{_locale}/serie/search" , name = "search_series" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function searchSeries(Request $request)
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('user_login');
        }

        $locale = $request->getLocale();
        $query = $request->request->get('query' );
        $sql = 'SELECT id FROM `titles` WHERE `show_id` IN (SELECT id FROM `shows` WHERE `type`="series") AND (`show_id` = "'.$query.'" OR `title` LIKE "%'.$query.'%")';

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
        return $this->render("Serie/index.html.twig", array('series' => $pagination));
    }

    /**
    * @Route(
     *     "/{_locale}/serie/delete/{id}" , name = "serie_delete" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function deleteSerie($id, Request $request)
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

        $this->em->remove($show);
        $this->em->flush();
        return $this->redirectToRoute('series');
    }

    /**
    * @Route(
     *     "/{_locale}/serie/update/{id}" , name = "serie_update" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function updateSerie($id, Request $request)
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('user_login');
        }
        $showRepository = $this->em->getRepository(Show::class);
        $show = $showRepository->findOneBy( array('id' => $id) );

        $this->addFlash('success', 'Série Modifié avec succés');
        return $this->redirectToRoute('series');
    }
}
