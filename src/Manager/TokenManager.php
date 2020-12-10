<?php

namespace App\Manager;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class TokenManager
{
    protected $em;
    public function __construct(EntityManagerInterface $em)
    {
      $this->em = $em;
    }
    
    public function isValid($token, $response)
    {
      if(!$token){
        $content['data'] = array(
            'type' => 'error'
        );
        $content['error_api'] = "token_not_valid";
        $response->setContent(json_encode($content));
        return $response;
      }else{
        $userRepository = $this->em->getRepository(User::class);
        $user = $userRepository->findOneBy( array('accessToken' => $token, 'enabled' => 1 ) );
        if(!$user){
            $content['data'] = array(
                'type' => 'error'
            );
            $content['error_api'] = "token_not_valid";
            $response->setContent(json_encode($content));
            return $response;
        }
      }
    return 'true';
  }
}
