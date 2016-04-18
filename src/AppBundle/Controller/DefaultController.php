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
         //getting the entity manganer as $em
         $em = $this->getDoctrine()->getManager();

        //$inventories = $em->getRepository('AppBundle:Inventory')->find();

        $inventories = $em->getRepository('AppBundle:Inventory');
        //Query builder
        $query = $inventories->createQueryBuilder('i')
            ->where('i.status_id = :status')
            ->setParameter('status', 'Available')
            ->getQuery();

        // $inventories = $em->getRepository('AppBundle:Inventory')
        //     ->findAll();

        // $itemStatusName = $inventories->getItemStatus()->getName();

        return $this->render('default/index.html.twig', array(
            'inventories' => $inventories,
        ));
     }
 }
