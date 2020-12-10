<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\User;
use App\Entity\File;
use App\Entity\Show;
use App\Entity\Link;
use App\Entity\Statistique;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use App\Entity\Episode;
use App\Manager\StatsManager;
use App\Manager\ShowManager;

class UserController extends Controller
{
    protected $em;
    protected $encoder;
	protected $statsManager;
	protected $showManager;
    public function __construct(ShowManager $showManager, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, StatsManager $statsManager)
    {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->statsManager = $statsManager;
        $this->showManager = $showManager;
    }

    /**
     * @Route("/admin/website/delete/{id}" , name="delete_website")
     */
    public function deleteWebSite($id, Request $request){
        $query = "DELETE FROM `websites` WHERE `id`=".$id;
        $statement = $this->em->getConnection()->prepare($query);
        $statement->execute();
        return $this->redirectToRoute('admin_websites');
    }

    /**
     * @Route("/admin/websites" , name="admin_websites")
     */
    public function adminWebsites(Request $request)
    {
        if ($request->isMethod('post')) {
            $query = 'INSERT INTO `websites`(`max`, `link`, `lang`) VALUES ('.$request->request->get('max').',"'.$request->request->get('link').'","'.$request->request->get('langue').'")';
            $statement = $this->em->getConnection()->prepare($query);
            $statement->execute();
            //dd($request->request);
        }
        $query = 'SELECT * FROM `websites` WHERE 1';
        $statement = $this->em->getConnection()->prepare($query);
        $statement->execute();
        $websites = $statement->fetchAll();
        return $this->render("admin/websites.html.twig", ['websites' => $websites]);
    }
     /**
     * @Route("/admin/links/check/status" , name="admin_links_check_status")
     */
    public function adminLinksCheckStatus(Request $request){
        $query = 'SELECT `value` FROM `status` WHERE `key`="checklinks"';
        $statement = $this->em->getConnection()->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll();
        $status = $result[0]['value'];
        $status = $status ? 0 : 1;
        $query = 'UPDATE `status` SET `value`='.$status.' WHERE `key`= "checklinks"';
        $statement = $this->em->getConnection()->prepare($query);
        $statement->execute();
        return $this->redirectToRoute('admin_files');
    }

    /**
     * @Route("/admin/links/check" , name="admin_links_check")
     */
    public function adminLinksCheck(Request $request){
        $query = 'SELECT `value` FROM `status` WHERE `key`="checklinks"';
        $statement = $this->em->getConnection()->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll();
        $status = $result[0]['value'];
        if($status){
            $linkRepository = $this->em->getRepository(Link::class);
            $limit = 10;
            $query = 'SELECT `value` FROM `status` WHERE `key`="links"';
            $statement = $this->em->getConnection()->prepare($query);
            $statement->execute();
            $result = $statement->fetchAll();
            $offset = $result[0]['value'];
            $result = $statement->fetchAll();
            $qb = $this->em->createQueryBuilder();
            $qb->select('l')
            ->from('App\Entity\Link', 'l')
            ->setFirstResult( $offset )
            ->setMaxResults($limit);
            $query = $qb->getQuery();
            $result = $query->getArrayResult();
            $i = 0;
            foreach($result as $link){
                $i++;
                $handle = curl_init($link['link']);
                curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
                $response = curl_exec($handle);
                $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
                if($httpCode == 404) {
                    $link = $linkRepository->findOneBy( array('link' => $link['link']) );
                    $this->em->remove($link);
                    $this->em->flush();
                    //echo '<pre>';print_r($link['link']);
                }
                curl_close($handle);
            }
            if($i < $limit){
                $offset = 0;
            }else{
                $offset = $offset + $i;
            }
            $query = 'UPDATE `status` SET `value`='.$offset.' WHERE `key` = "links"';
            $statement = $this->em->getConnection()->prepare($query);
            $statement->execute();
        }
        return new JsonResponse(true);
    }

    /**
     * @Route("/admin/files/delete/{id}" , name="delete_file")
     */
    public function deleteFile($id, Request $request){
        $fileRepository = $this->em->getRepository(File::class);
        $file = $fileRepository->findOneBy( array('id' => $id) );
        try{
            unlink('upload/'.$file->getTitle().'.csv');
        }catch(\Exception $e){
            echo '<pre>';print_r($e->getMessage());
        }
        $this->em->remove($file);
        $this->em->flush();
        return $this->redirectToRoute('admin_files');
    }
    /**
     * @Route("/admin/files/scrapping" , name="admin_files_scrapping")
     */
    public function adminFilesScrapping(Request $request){
        $fileRepository = $this->em->getRepository(File::class);
        $showRepository = $this->em->getRepository(Show::class);
        $linkRepository = $this->em->getRepository(Link::class);
        $files = $fileRepository->findAll();
        foreach($files as $file){
            if( $file->fileExist() && ($file->getReached() < $file->getTotal()) ){
                $handle = fopen('upload/'.$file->getTitle().'.csv','r');
                $data = fgetcsv($handle);
                $datats = array();
                while ( ($data = fgetcsv($handle) ) !== FALSE ) {
                    array_push($datats, $data);
                }
                $reached = $file->getReached();
				
                for($i = 0; $i < 10; $i++){
                    if(isset($datats[$reached])){
                        $data = $datats[$reached];
                        $imdb = $data[0];
                        if(count(explode('tt', $imdb))>1){
                            $showExist = $showRepository->findOneBy( array('imdb' => $imdb) );
                        }else{
                            $showExist = $showRepository->findOneBy( array('tmdb' => $imdb) );
                        }
                        if(!$showExist){
                            if(count(explode('tt', $imdb))>1){
                                $showInfos = $this->showManager->getInfos($imdb, null);
                            }else{
                                $showInfos = $this->showManager->getInfos(null, $imdb);
                            }
                            if(isset($showInfos['title'])){
                                $showExist = new Show();
                                $showExist->setTitle($showInfos['title']);
                                $showExist->setId($showInfos['imdb']);
                                $showExist->setImdb($showInfos['imdb']);
                                $showExist->setTmdb($showInfos['tmdb']);
                                $showExist->setYear($showInfos['year']);
                                $showExist->setType($showInfos['type']);
                                $showExist->setPoster('http://image.tmdb.org/t/p/w500'.$showInfos['poster']);
                                $showExist->setType($showInfos['type']);
                                $this->em->persist($showExist);
                                $this->em->flush();
                                $sTitle = $this->showManager->setShowTitle($file->getLang(), $showExist);
                                $showExist->addTitle($sTitle);
                                $this->em->merge($showExist);
                                $this->em->flush();
                            }
                        }
                        
						for($i=1;$i<5;$i++){
							$link = $data[$i];
							if($link){
								$linkExist = $linkRepository->findOneBy( array('link' => $link) );
								if(!$linkExist && $showExist){
									$linkN = new Link();
									$ls = explode('?v=',$link);
									if(count($ls)>=2){
										$l = 'https://www.youtube.com/embed/'.$ls[1];
										$linkN->setLink($l);
									}else{
										$linkN->setLink($link);
									}
									$linkN->setShow($showExist);
									$linkN->setLang($file->getLang());
									$this->em->persist($linkN);
									$this->em->flush();
								}
							}
						}
                        $reached++;
                    }
                }
                $file->setReached($reached);
                $this->em->merge($file);
                $this->em->flush();
                break;
            }
        }
        return new JsonResponse(true);
    }
    /**
     * @Route("/admin/files" , name="admin_files")
     */
    public function adminFiles(Request $request)
    {
        $query = 'SELECT `value` FROM `status` WHERE `key`="checklinks"';
        $statement = $this->em->getConnection()->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll();
        $status = $result[0]['value'];
        $fileRepository = $this->em->getRepository(File::class);
        if ($request->isMethod('post')) {
            $extension = explode('.',$_FILES["csv"]['name'])[1];
            if($extension != 'csv'){
                $this->addFlash('error', 'Csv file required!');
            }else{
                $title = $request->request->get('title');
                $fileExist = $fileRepository->findOneBy( array('title' => $title) );
                if($fileExist){
                    $this->addFlash('error', 'File Name already exists!');
                }else{
                    $langue = $request->request->get('langue');
                    $target_dir = "upload/";
                    $target_file = $target_dir . basename($request->request->get('title')).'.'.$extension;
                    if (move_uploaded_file($_FILES["csv"]["tmp_name"], $target_file)) {
                        $handle = fopen('upload/'.$title.'.csv','r');
                        $data = fgetcsv($handle);
                        $datats = array();
                        while ( ($data = fgetcsv($handle) ) !== FALSE ) {
                            array_push($datats, $data);
                        }
                        $file = new File();
                        $file->setTitle($title);
                        $file->setLang($langue);
                        $file->setTotal(count($datats));
                        $file->setReached(0);
                        $this->em->persist($file);
                        $this->em->flush();
                        $this->addFlash('success', 'File uploaded successfully!');
                    }else{
                        $this->addFlash('error', 'Failed uploading file!');
                    }
                }
            }
        }
        $files = $fileRepository->findAll();
        return $this->render("admin/files.html.twig", ['files' => $files, 'status' => $status]);
    }

    /**
    * @Route(
     *     "/acceuil" , name = "acceuil"
     * )
     */
    public function Acceuil(Request $request)
    {
        return $this->render("acceuil.html.twig");
    }

    /**
    * @Route(
     *     "/{_locale}/admin" , name = "admin" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function Admin(Request $request)
    {  
        if(!$this->getUser()){
            return $this->redirectToRoute('user_login');
        }else{
            if($this->getUser()->hasRole("ROLE_ADMIN")){
                return $this->render("index.html.twig");
            }
            return $this->redirectToRoute('films');
        }
    }

    /**
    * @Route("/")
    * @Route(
     *     "/{_locale}" , name = "home" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function Home(Request $request)
    {
        return $this->redirectToRoute('acceuil');
    }

    /**
    * @Route(
     *     "/{_locale}/user/liste" , name = "users" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function Users(Request $request)
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('user_login');
        }
        $userRepository = $this->em->getRepository(User::class);
        $users = $userRepository->findAll( );
        return $this->render("User/index.html.twig", array('users' => $users));
    }

    /**
    * @Route(
     *     "/{_locale}/user/add" , name = "user_add" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
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
        	$userName = $request->request->get('userName');

            $user = $userRepository->findOneBy( array('username' => $userName) );
            if($user){
                $this->addFlash('error', "Nom d'utilisateur déja existant!");
                return $this->render("User/edit.html.twig",array('user' => $user));
            }
            
            $webSite = $request->request->get('webSite');
            $user = $userRepository->findOneBy( array('webSite' => $webSite) );
            if($user){
                $this->addFlash('error', "Site web déja existant!");
                return $this->render("User/edit.html.twig",array('user' => $user));
            }
            
            $user = $userRepository->findOneBy( array('email' => $email) );
            if($user){
                $this->addFlash('error', "Email web déja existant!");
                return $this->render("User/edit.html.twig",array('user' => $user));
            }

        	$user = new User();
        	$user->setName($name);
        	$user->setLastName($lastName);
        	$user->setuserName($userName);
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
    * @Route(
     *     "/{_locale}/user/edit/{id}" , name = "user_edit" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function userEdit($id, Request $request)
    {
        $userRepository = $this->em->getRepository(User::class);
        $user = $userRepository->findOneBy( array('id' => $id) );
        if ($request->isMethod('post')) {
            $name = $request->request->get('name');
            $lastName = $request->request->get('lastName');
            $webSite = $request->request->get('webSite');
            $role = $request->request->get('role');
            $email = $request->request->get('email');
            $userName = $request->request->get('userName');
            $user->setName($name);
            $user->setLastName($lastName);
            $user->setuserName($userName);
            $user->setWebSite($webSite);
            $mdp = $request->request->get('pass');
            if($mdp){
                $user->setPlainPassword($mdp);
            }
            $user->setEmail($email);
            $user->setEnabled(1);
            if($role==1){
                $roles[] = 'ROLE_ADMIN';
            }
            if($role==2){
                $roles[] = 'ROLE_CLIENT';
            }
            $user->setRoles($roles);
            $this->em->merge($user);
            $this->em->flush();
        }
        return $this->render("User/edit.html.twig",array('user' => $user));
    }

    /**
     * @Route("/register-user" , name="user_register")
     */
    public function userRegister(Request $request)
    {
    	if ($request->isMethod('post')) {
        	$name = $request->request->get('name');
        	$lastName = $request->request->get('lastName');
            $userName = $request->request->get('userName');
        	$mdp = $request->request->get('mdp');
        	$cmdp = $request->request->get('cmdp');
        	$role = $request->request->get('role');
        	$email = $request->request->get('email');

        	if($mdp!=$cmdp){
        	    $this->addFlash('error', "Mot de passes ne sont pas les mémes!");
	            return $this->redirectToRoute('user_register');
        	}

        	$userRepository = $this->em->getRepository(User::class);
            
            $user = $userRepository->findOneBy( array('username' => $userName) );
            if($user){
                $this->addFlash('error', "Nom d'utilisateur déja existant!");
	            return $this->redirectToRoute('user_register');
            }
            
        	$webSite = $request->request->get('webSite');
        	$user = $userRepository->findOneBy( array('webSite' => $webSite) );
            if($user){
                $this->addFlash('error', "Site web déja existant!");
	            return $this->redirectToRoute('user_register');
            }
            
        	$user = $userRepository->findOneBy( array('email' => $email) );
            if($user){
                $this->addFlash('error', "Email web déja existant!");
	            return $this->redirectToRoute('user_register');
            }
            
        	$user = new User();
        	$user->setName($name);
        	$user->setEnabled(1);
        	$user->setLastName($lastName);
            $user->setuserName($userName);
        	$user->setWebSite($webSite);
	        $token = bin2hex(random_bytes(20));
        	$user->setAccessToken($token);
        	$user->setPlainPassword($mdp);
        	$user->setEmail($email);
    		$roles[] = 'ROLE_CLIENT';
        	$user->setRoles($roles);
        	$this->em->persist($user);
        	$this->em->flush();
        	$this->addFlash('success', 'Inscription effectué avec succés!');
	        return $this->redirectToRoute('user_login');
        }
        return $this->render("User/register.html.twig");
    }

    /**
     * @Route("/logout" , name="user_logout")
     */
    public function userLogout(Request $request)
    {
        $this->get('security.token_storage')->setToken(null);
        $request->getSession()->invalidate();
        return $this->redirectToRoute('user_login');
    }

    /**
     * @Route("/login" , name="user_login")
     */
    public function userLogin(Request $request)
    {
        $error = "";
        if ($request->isMethod('post')) {
            $_username = $request->request->get('_username');
            $_password = $request->request->get('_password');


            $user_manager = $this->get('fos_user.user_manager');
            $user = $user_manager->findUserByUsername($_username);

            $user = $this->getDoctrine()->getManager()->getRepository("App:User")->findOneBy(array('username' => $_username));

            if($user){
                $encoder = $this->encoder;
                $salt = $user->getSalt();

                if($encoder->isPasswordValid($user, $_password)) {
                    $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                    $this->get('security.token_storage')->setToken($token);

                    
                    $this->get('session')->set('_security_main', serialize($token));
                    
                    $event = new InteractiveLoginEvent($request, $token);
                    $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
                    return $this->redirectToRoute('admin');
                }else{
                    $error = 'Mot de passe incorrecte';
                }
            }else{
                $error = 'User not found';
            }
        }

        return $this->render("User/login.html.twig" , array('error' => $error) );
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
    * @Route(
     *     "/{_locale}/show/iframe/{accessToken}/{imdb}" , name = "show_iframe" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function showIframe($accessToken, $imdb, Request $request)
    {
    	$userRepository = $this->em->getRepository(User::class);
        $user = $userRepository->findOneBy( array('accessToken' => $accessToken) );
        //echo "<pre>";print_r(!$user);exit;
        if(!$user){
           return $this->render("shownotfound.html.twig");
        }

        $ShowRepository = $this->em->getRepository(Show::class);
        $show = $ShowRepository->findOneBy( array('imdb' => $imdb) );
		$showTmdb = $ShowRepository->findOneBy( array('tmdb' => $imdb) );

        $episodeRepository = $this->em->getRepository(Episode::class);
        $episode = $episodeRepository->findOneBy( array('imdb' => $imdb) );

        if(!$show and !$episode and !$showTmdb){
	       return $this->render("shownotfound.html.twig");
        }

        if(!$show){
			if(!$episode){
				$show = $showTmdb;
			}else{
				$show = $episode;
			}
        }
        $this->statsManager->incremente($user, 1);
		return $this->render("videoPlayer.html.twig", array('show'=>$show));
    }

    /**
    * @Route(
     *     "/{_locale}/user/api" , name = "user_api" ,
     *     requirements={
     *         "_locale": "en|fr|de|es|it"
     *     }
     * )
     */
    public function userApi(Request $request)
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('user_login');
        }
        return $this->render("User/api.html.twig");
    }
}
