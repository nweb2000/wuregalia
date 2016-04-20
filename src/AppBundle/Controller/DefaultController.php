<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Inventory;
use AppBundle\Entity\Reservation;
use AppBundle\Form\InventoryType;

 class DefaultController extends Controller
 {
     /**
      * @Route("/", name="homepage")
      */
     public function indexAction(Request $request)
     {
         //getting the entity manganer as $em
        $em = $this->getDoctrine()->getManager();

        $avalStatus = $em->getRepository('AppBundle:Status')->findOneByName('AVAL'); //place whatever you available status name is here
        $inventories = $em->getRepository('AppBundle:Inventory')->findByItemStatus($avalStatus->getId());

        return $this->render('default/index.html.twig', array(
            'inventories' => $inventories
        ));
     }

    /**
     * Filter availItems by filter
     *
     * @Route("/filter/{filter}", name="inventory_filter")
     * @Method("GET")
     *
     */
     public function filterAction(Request $request)
     {
        $em = $this->getDoctrine()->getManager();
        $avalStatus = $em->getRepository('AppBundle:Status')->findOneByName('AVAL'); //place whatever you available status name is here
        $inventories = $em->getRepository('AppBundle:Inventory')->findByItemStatus($avalStatus->getId());

        $returnItems = null;

        foreach ($inventories as $inventory)
        {
            if ($inventory->getItemType() == $request)
            {
                $returnItems += $inventory;
            }
        }

        return $this->render('default/index.html.twig', array (
            'inventories' => $returnItems
        ));
     }

 }
