<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Episode;

class EpisodeController extends Controller
{
    protected $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /**
     * @Route("/episode/liste" , name="episodes")
     */
    public function Episodes(Request $request)
    {
        $episodeRepository = $this->em->getRepository(Episode::class);
        $episodes = $episodeRepository->findAll();
        return $this->render("Episode/index.html.twig", array('episodes' => $episodes));
    }
}
