<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Show;
use Doctrine\ORM\EntityManagerInterface;

class AnimeController extends Controller
{
    protected $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /**
     * @Route("/anime/liste" , name="animes")
     */
    public function Animes(Request $request)
    {
        $showRepository = $this->em->getRepository(Show::class);
        $animes = $showRepository->findBy( array('type' => 'anime') );
        return $this->render("Anime/index.html.twig", array('animes' => $animes));
    }

    /**
     * @Route("/anime/add" , name="add_anime")
     */
    public function addAnime(Request $request)
    {
        return $this->render("Anime/add.html.twig");
    }
}
