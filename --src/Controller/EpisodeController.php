<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Episode;
use Knp\Component\Pager\PaginatorInterface;

class EpisodeController extends Controller
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
     *     "/{_locale}/episode/liste" , name = "episodes" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function Episodes(Request $request)
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('user_login');
        }
        $qb = $this->em->createQueryBuilder();
        $qb->select('e')
           ->from('App:Episode', 'e')
           ->orderBy('e.id', 'DESC');

        $pagination = $this->paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            50/*limit per page*/
        );

        return $this->render("Episode/index.html.twig", array('episodes' => $pagination));
    }

    /**
    * @Route(
     *     "/{_locale}/episode/search" , name = "search_episodes" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function searchEpisodes(Request $request)
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('user_login');
        }
        $query = $request->request->get('query' );
        $qb = $this->em->createQueryBuilder();
        $qb->select('e')
           ->from('App:Episode', 'e')
           ->where('e.title LIKE ?1')
           ->orderBy('e.id', 'DESC');
        $qb->setParameter(1, '%'.$query.'%');
        $pagination = $this->paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            50/*limit per page*/
        );
        return $this->render("Episode/index.html.twig", array('episodes' => $pagination));
    }

    /**
    * @Route(
     *     "/{_locale}/episode/noVid" , name = "episode_with_no_videos" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function episodeNoVideos(Request $request)
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('user_login');
        }
        $sql = 'SELECT `episode_id` FROM `links` WHERE 1';

        $statement = $this->em->getConnection()->prepare($sql);
        $statement->execute();

        $results = $statement->fetchAll();
        $ids = [];
        foreach ($results as $key => $result) {
           $id = $result['episode_id'];
           if(!in_array($id, $ids)){
            array_push($ids, $id);
           }
        }

        $query = $request->request->get('query');
        $qb = $this->em->createQueryBuilder();
        $qb->select('e')
           ->from('App:Episode', 'e')
           ->andWhere('e.id NOT IN (:ids)')
           ->orderBy('e.id', 'DESC');
        $qb->setParameter('ids', $ids);
        $query = $qb->getQuery();

        $pagination = $this->paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            50/*limit per page*/
        );
        return $this->render("Episode/index.html.twig", array('episodes' => $pagination));
    }

     /**
    * @Route(
     *     "/{_locale}/episode/delete/{id}" , name = "episode_delete" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function deleteEpisode($id, Request $request)
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('user_login');
        }
        $episodeRepository = $this->em->getRepository(Episode::class);
        $episode = $episodeRepository->findOneBy( array('id' => $id) );

        foreach ($episode->getLinks() as $link) {
            $this->em->remove($link);
            $this->em->flush();
        }
        $this->em->remove($episode);
        $this->em->flush();
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }
}
