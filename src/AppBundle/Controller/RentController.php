<?php
/* *******************************************
    Rent Controller
    Controller for the Rental page,
    allows users to reserve there regalia
********************************************* */
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Inventory;
use AppBundle\Entity\Reservation;
use AppBundle\Form\InventoryType;

/**
 * rental controller.
 *
 * @Route("/rent")
 */
class RentController extends Controller
{
    /**
     * @Route("/", name="rent")
     * @Method("GET")
     */

     //Shows everything in the inventory that is rentable

    public function showAction()
    {
        $em = $this->getDoctrine()->getManager();

        $avalStatus = $em->getRepository('AppBundle:Status')->findOneByName('AVAL'); //place whatever you available status name is here
        $avalInv = $em->getRepository('AppBundle:Inventory')->findByItemStatus($avalStatus->getId()); //place whatever your available status name is here

        return $this->render('rent/show.html.twig', array(
            'aval' => $avalInv
        ));
    }

    /**
     * @Route("/{id}", name="rent_item")
     * @Method("GET")
     */
     
    public function rentAction(Inventory $item) 
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) 
        {
            throw $this->createAccessDeniedException();
        }
        
        $em = $this->getDoctrine()->getManager();
        $sessionUser = $this->getUser();
        $user = $em->getRepository('AppBundle:User')->findOneByUsername($sessionUser->getUsername()); //get the user entity of session user
        $reservation = new Reservation($user, $item);
        $em = $this->getDoctrine()->getManager();
        $em->persist($reservation);
        $em->flush();

        return $this->render('rent/rent.html.twig', array(
            'item' => $item
        ));

    }

    
}
