<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\User;
use App\Entity\Show;
use App\Entity\Statistique;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
	protected $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

     /**
     * @Route("/acceuil" , name="acceuil")
     */
    public function Acceuil(Request $request)
    {
        return $this->render("index.html.twig");
    }

    /**
     * @Route("/" , name="home")
     */
    public function Home(Request $request)
    {
        return $this->render("acceuil.html.twig");
    }

    /**
     * @Route("/user/liste" , name="users")
     */
    public function Users(Request $request)
    {
        $userRepository = $this->em->getRepository(User::class);
        $users = $userRepository->findAll( );
        return $this->render("User/index.html.twig", array('users' => $users));
    }

    /**
     * @Route("/user/add" , name="user_add")
     */
    public function userAdd(Request $request)
    {
    	if ($request->isMethod('post')) {
        	$name = $request->request->get('name');
        	$lastName = $request->request->get('lastName');
        	$mdp = $request->request->get('pass');
        	$webSite = $request->request->get('webSite');
        	$role = $request->request->get('role');
        	$email = $request->request->get('email');
        	$user = new User();
        	$user->setName($name);
        	$user->setLastName($lastName);
        	$user->setuserName($name.'.'.$lastName);
        	$user->setWebSite($webSite);
	        $token = bin2hex(random_bytes(20));
        	$user->setAccessToken($token);
        	$user->setPlainPassword($mdp);
        	$user->setEmail($email);
        	$user->setEnabled(1);
        	if($role==1){
        		$roles[] = 'ROLE_ADMIN';
        	}
        	if($role==2){
        		$roles[] = 'ROLE_CLIENT';
        	}
        	$user->setRoles($roles);
        	$this->em->persist($user);
        	$this->em->flush();
        }
        return $this->render("User/add.html.twig");
    }

    /**
     * @Route("/register-user" , name="user_register")
     */
    public function userRegister(Request $request)
    {
    	if ($request->isMethod('post')) {
        	$name = $request->request->get('name');
        	$lastName = $request->request->get('lastName');
        	$mdp = $request->request->get('mdp');
        	$webSite = $request->request->get('webSite');
        	$role = $request->request->get('role');
        	$email = $request->request->get('email');
        	$user = new User();
        	$user->setName($name);
        	$user->setLastName($lastName);
        	$user->setuserName($name.'.'.$lastName);
        	$user->setWebSite($webSite);
	        $token = bin2hex(random_bytes(20));
        	$user->setAccessToken($token);
        	$user->setPlainPassword($mdp);
        	$user->setEmail($email);
    		$roles[] = 'ROLE_CLIENT';
        	$user->setRoles($roles);
        	$this->em->persist($user);
        	$this->em->flush();
	        return $this->redirectToRoute('user_login');
        }
        return $this->render("User/register.html.twig");
    }

    /**
     * @Route("/login-user" , name="user_login")
     */
    public function userLogin(Request $request)
    {
        return $this->render("User/login.html.twig");
    }

    /**
     * @Route("/user/update_status" , name="update_user_status")
     */
    public function userUpdateStatus(Request $request)
    {
    	$id_user = $request->request->get('id_user');
    	$userRepository = $this->em->getRepository(User::class);
        $user = $userRepository->findOneBy( array('id' => $id_user) );
        if($user->isEnabled()){
        	$user->setEnabled(0);
        }else{
        	$user->setEnabled(1);
        }
        $this->em->merge($user);
        $this->em->flush();
        return new JsonResponse(array('enabled' => $user->isEnabled()));
    }

    /**
     * @Route("/show/iframe/{accessToken}/{imdb}" , name="show_iframe")
     */
    public function showIframe($accessToken, $imdb, Request $request)
    {
    	$userRepository = $this->em->getRepository(User::class);
        $user = $userRepository->findOneBy( array('accessToken' => $accessToken) );
        if(!$user){
        	echo "<pre>";print_r('utilisateur not found');
        }

        $ShowRepository = $this->em->getRepository(Show::class);
        $show = $ShowRepository->findOneBy( array('imdb' => $imdb) );
        if(!$user){
        	echo "<pre>";print_r('Show not found');
        }
        $date = date("Y-m-d");
        $statRepository = $this->em->getRepository(Statistique::class);
        $stat = $statRepository->findOneBy( array('creationDate' => new \DateTime(), 'user' => $user->getId() ));
    	if($stat){
    		$stat->setCount($stat->getCount()+1);
    		$this->em->merge($stat);
    	}else{
    		$stat = new Statistique();
    		$stat->setCount(1);
    		$stat->setType(1);
    		$stat->setUser($user);
    		$stat->setCreationDate(new \DateTime());
    		$this->em->persist($stat);
    	}
    	$this->em->flush();
    	exit('done');
    }
}
