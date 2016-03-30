<?php
/* --------------------------------------------------
    LoginController.php
    Handles sign ins, authenticates users via LDAP
    Author: Noah Weber
------------------------------------------------------- */

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Form\LoginType;
use Symfony\Component\Ldap\Exception\ConnectionException;

class LoginController extends Controller
{

    /**
     * Login controller.
     *
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
        $authUtils = $this->get('security.authentication_utils');
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return $this->render(
            'base.html.twig',
            array(
                // last username entered by the user
                'last_username' => $lastUsername,
                'error'         => $error
            ));
    }

}
