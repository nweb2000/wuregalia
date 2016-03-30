<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Inventory;

 class DefaultController extends Controller
 {
     /**
      * @Route("/", name="homepage")
      */
     public function indexAction(Request $request)
     {
         $em = $this->getDoctrine()->getManager();

        $inventories = $em->getRepository('AppBundle:Inventory')->findAll();

        return $this->render('default/index.html.twig', array(
            'inventories' => $inventories,
        ));
     }
 }
