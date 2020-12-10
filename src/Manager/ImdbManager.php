<?php

namespace App\Manager;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Show;

class ImdbManager
{
    protected $em;
    public function __construct(EntityManagerInterface $em)
    {
      $this->em = $em;
    }
    
    public function isValid($imdb, $response)
    {
      $ShowRepository = $this->em->getRepository(Show::class);
      $show = $ShowRepository->findOneBy( array('imdb' => $imdb) );
	  if(!$show){
		  $show = $ShowRepository->findOneBy( array('tmdb' => $imdb) );
	  }
      if(!$show){
          $content['data'] = array(
              'type' => 'error'
          );
          $content['error_api'] = "show_not_found";
          $response->setContent(json_encode($content));
          return $response;
     }  
    return 'true';
  }
}
