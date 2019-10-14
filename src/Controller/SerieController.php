<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Show;
use Doctrine\ORM\EntityManagerInterface;

class SerieController extends Controller
{
    protected $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/serie/liste" , name="series")
     */
    public function Series(Request $request)
    {
        $showRepository = $this->em->getRepository(Show::class);
        $series = $showRepository->findBy( array('type' => 'series') );
        return $this->render("Serie/index.html.twig", array('series' => $series));
    }

    /**
     * @Route("/serie/add" , name="add_serie")
     */
    public function addSerie(Request $request)
    {
        return $this->render("Serie/add.html.twig");
    }
}
