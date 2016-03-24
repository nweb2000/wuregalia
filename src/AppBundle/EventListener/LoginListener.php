<?php
/* *********************************************************
    LoginListener
    An event listener which listens to login related events
************************************************************ */
namespace AppBundle\EventListener;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Ldap\Exception\ConnectionException;
use Symfony\Component\Ldap\LdapClientInterface;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;

class LoginListener
{
    protected $em;  //Doctrine Entity Manager
    protected $userToken; //User returned from token storage is of type Symfony\Component\Security\Core\User\User
    protected $ldap; //ldap component
    protected $searchDn;
    protected $searchPassword;
    protected $baseDn;
    protected $searchQuery;
    
    /* ----------------------------------------------------------------
        params:
        EntityManager $em
        TokenStorageInterface $tokenStarage
        LdapClientInterface $ldap
        string $searchDn, $searchPassword, $baseDn, $searchQuery
    ----------------------------------------------------------- */
    public function __construct($em, $tokenStorage, $ldap, $searchDn, $searchPassword, $baseDn, $searchQuery) 
    {
       $this->em = $em; 
       $this->userToken = $tokenStorage->getToken()->getUser();
       $this->ldap = $ldap;
       $this->searchDn = $searchDn;
       $this->searchPasword = $searchPassword;
       $this->baseDn = $baseDn;
       $this->searchQuery= $searchQuery;
    }

    /* -----------------------------------------------------------
        onInteractiveLogin()
        Once the use actually logs in (as in enter there username
        and password successfully) we need to see if user is in 
        the app database, if not we add them to it.
    --------------------------------------------------------------- */

    //For this method we need to connect to Ldap so search for extra user info, since sympony's ldap user provider 
    //does not grab any extra information as of now
    //So yes a little inefficient...
    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $userRepos = $this->em->getRepository('AppBundle:User'); 
        $username = $this->userToken->getUsername();
        $user = $userRepos->findOneByUsername($username); //check if user already in database
        if(!$user) //if not in db, fetch info from ldap and create entity to put in db
        {
            try {
        //        $this->ldap->bind($this->searchDn, $this->searchPassword);
                $query = str_replace('{username}', $username, $this->searchQuery);
                $result= $this->ldap->find($this->baseDn, $query);
            } catch (ConnectionException $e) {
                throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username), 0, $e);
            }
            $this->addUserFromActiveDir($result, $username);
        }
    }

    //Place use in database with information from Activy Directory
    protected function addUserFromActiveDir($result, $username) 
    {
        //extract all the info from result
        $result = $result[0];
        $displayName = $result['displayname'][0];
        
        $split = explode(", ", $displayName);

        if(count($split) == 2) 
        {
            $lname = $split[0];
            $fname = explode(" ", $split[1])[0];
        }
        else
        {
            $split = explode(" ", $split[0]);
            $lname = $split[0];
            $fname = $split[1];
        }

        if(isset($result['mail'])) 
        {
            $email = $result['mail'][0];
        }
        else 
        {
            $email = $username . '@mailbox.winthrop.edu';
        }

        $user = new User(); //note that this is a AppBundle/Entity/User object
        $user->setUsername($username);
        $user->setLname($lname);
        $user->setEmail($email);
        $user->setFname($fname);
        $this->em->persist($user);
        $this->em->flush();
    }
}

?>
