<?php

namespace App\Manager;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Statistique;

class StatsManager
{
    protected $em;
    public function __construct(EntityManagerInterface $em)
    {
      $this->em = $em;
    }
    
    public function incremente($user, $type)
    {
        $date = date("Y-m-d");
        $statRepository = $this->em->getRepository(Statistique::class);
        $stat = $statRepository->findOneBy( array('creationDate' => new \DateTime(), 'user' => $user->getId(), 'type' => $type ));
        if($stat){
            $stat->setCount($stat->getCount()+1);
            $this->em->merge($stat);
        }else{
            $stat = new Statistique();
            $stat->setCount(1);
            $stat->setType($type);
            $stat->setUser($user);
            $stat->setCreationDate(new \DateTime());
            $this->em->persist($stat);
        }
        $this->em->flush();
  }
}
