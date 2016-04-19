<?php
/**
 Peter Resch
 Controllers in this file populate reports and allows conversion of reports to pdf format for printing or downloading
 */
 
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;


use AppBundle\Entity\Inventory;
use AppBundle\Entity\Status;
use AppBundle\Entity\User;
use AppBundle\Entity\ItemType;
use AppBundle\Entity\Major;
use AppBundle\Entity\School;
use AppBundle\Entity\Color;
use AppBundle\Entity\Reservation;




class ReportsController extends Controller
{
    /**
     * @Route("/reports", name="reports")
     * displays list of possible reports
     */
    public function indexAction(Request $request)
    {
    	return $this->render('reports/index.html.twig');	    
    }
    
    /**
     * @Route("/reports/users", name="showUsers")
     * displays all users
     */
    public function showUsers(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:User')->findAll();

        return $this->render('reports/users.html.twig', array(
            'users' => $users,
        ));
    }
    
    /**
     * @Route("/reports/users/pdf", name="createUsersPDF")
     * generate a pdf displaying all users
     */
    public function createUsersPDF(Request $request)
    {
    	    $em = $this->getDoctrine()->getManager();
    	    $users = $em->getRepository('AppBundle:User')->findAll();
    	    
	    $html = $this->renderView('reports/users.generatepdf.html.twig', array('users'  => $users));
	
	    return new Response(
	    $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
	    200,
	    array(
		'Content-Type'          => 'application/pdf',
		'Content-Disposition'   => 'attachment; filename="file.pdf"'
	    ));
    }
    
    /**
     * @Route("/reports/admins", name="showAdmins")
     * displays administrators
     */
    public function showAdmins(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:User')->findByAdminpriv(1);

        return $this->render('reports/users_admins.html.twig', array(
            'users' => $users,
        ));
    }
    
    /**
     * @Route("/reports/admins/pdf", name="createAdminsPDF")
     * generate a pdf displaying all administrators
     */
    public function createAdminsPDF(Request $request)
    {
    	    $em = $this->getDoctrine()->getManager();
    	    $users = $em->getRepository('AppBundle:User')->findByAdminpriv(1);
    	    
	    $html = $this->renderView('reports/users_admins.generatepdf.html.twig', array('users'  => $users));
	
	    return new Response(
	    $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
	    200,
	    array(
		'Content-Type'          => 'application/pdf',
		'Content-Disposition'   => 'attachment; filename="file.pdf"'
	    ));
    }
    
    /**
     * @Route("/reports/registeredUsers", name="showRegisteredUsers")
     * displays administrators
     */
    public function showRegisteredUsers(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AppBundle:User')->findByAdminpriv(0);

        return $this->render('reports/users_registered.html.twig', array(
            'users' => $users,
        ));
    }
    
    /**
     * @Route("/reports/registeredUsers/pdf", name="createRegisteredUsersPDF")
     * generate a pdf displaying all administrators
     */
    public function createRegisteredUsersPDF(Request $request)
    {
    	    $em = $this->getDoctrine()->getManager();
    	    $users = $em->getRepository('AppBundle:User')->findByAdminpriv(0);
    	    
	    $html = $this->renderView('reports/users_registered.generatepdf.html.twig', array('users'  => $users));
	
	    return new Response(
	    $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
	    200,
	    array(
		'Content-Type'          => 'application/pdf',
		'Content-Disposition'   => 'attachment; filename="file.pdf"'
	    ));
    }
    
    /**
     * @Route("/reports/users/rentalHistory/{userID}", name="showUserRentalHistory")
     * display rental history of selected user (userID)
     */
    public function showUserRentalHistory(Request $request, $userID = 0)
    {
    	$em = $this->getDoctrine()->getManager();
        $reservations = $em->getRepository('AppBundle:Reservation')->findByUserid($userID);

        return $this->render('reports/users.rentalHistory.html.twig', array(
            'user' => $reservations,
        ));
    }
    
    /**
     * @Route("/reports/users/rentalHistory/pdf/{userID}", name="createUserRentalHistoryPDF")
     * generate a pdf displaying the rental history of selected user (userID
     */
    public function createUserRentalHistoryPDF(Request $request, $userID = 0)
    {
    	    $em = $this->getDoctrine()->getManager();
    	    $reservations = $em->getRepository('AppBundle:Reservation')->findByUserid($userID);
    	    
	    $html = $this->renderView('reports/users.rentalHistory.generatepdf.html.twig', array('user'  => $reservations));
	
	    return new Response(
	    $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
	    200,
	    array(
		'Content-Type'          => 'application/pdf',
		'Content-Disposition'   => 'attachment; filename="file.pdf"'
	    ));
    }
    	  
    
    /**
     * @Route("/reports/inventory", name="showInventory")
     * displays all inventory items, both rented and availible
     */
    public function showInventory(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
    	$itemStatuses = $em->getRepository('AppBundle:Status')->findby(array('name' => array('AVAL','PENDING_DONATION','RENTED','PENDING_ARRIVAL')));
        $itemStatusIDs = array();
        foreach ($itemStatuses as $Status)
        {
		$itemStatusIDs[] = $Status->getId();
        }	
        $inventory = $em->getRepository('AppBundle:Inventory')->findBy(array('itemStatus' => $itemStatusIDs));

        return $this->render('reports/inventory.html.twig', array(
            'items' => $inventory,
        ));
    }
    
    /**
     * @Route("/reports/inventory/pdf", name="createInventoryPDF")
     * generate a pdf displaying all inventory items, both rented and availible
     */
    public function createInventoryPDF(Request $request)
    {
    	    $em = $this->getDoctrine()->getManager();
    	    $itemStatuses = $em->getRepository('AppBundle:Status')->findby(array('name' => array('AVAL','PENDING_DONATION','RENTED','PENDING_ARRIVAL')));
	    $itemStatusIDs = array();
	    foreach ($itemStatuses as $Status)
	    {
	    	    $itemStatusIDs[] = $Status->getId();
	    }	
	    $inventory = $em->getRepository('AppBundle:Inventory')->findBy(array('itemStatus' => $itemStatusIDs));
	    $html = $this->renderView('reports/inventory.generatepdf.html.twig', array('items' => $inventory));
	
	    return new Response(
	    $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
	    200,
	    array(
		'Content-Type'          => 'application/pdf',
		'Content-Disposition'   => 'attachment; filename="file.pdf"'
	    ));
    }
    
    /**
     * @Route("/reports/inventory/availible", name="showAvailibleInventory")
     * displays availible inventory items
     */
    public function showAvailibleInventory(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
    	$avalStatus = $em->getRepository('AppBundle:Status')->findOneBy(array('name' => array('AVAL')));
        $inventory = $em->getRepository('AppBundle:Inventory')->findByItemStatus($avalStatus->getId());

        return $this->render('reports/inventory_availible.html.twig', array(
            'items' => $inventory,
        ));
    }
    
    /**
     * @Route("/reports/inventory/availible/pdf", name="createAvailibleInventoryPDF")
     * generate a pdf displaying availible inventory items
     */
    public function createAvailibleInventoryPDF(Request $request)
    {
    	    $em = $this->getDoctrine()->getManager();
    	    $avalStatus = $em->getRepository('AppBundle:Status')->findOneBy(array('name' => array('AVAL')));
            $inventory = $em->getRepository('AppBundle:Inventory')->findByItemStatus($avalStatus->getId());
    	    
	    $html = $this->renderView('reports/inventory_availible.generatepdf.html.twig', array('items' => $inventory));
	
	    return new Response(
	    $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
	    200,
	    array(
		'Content-Type'          => 'application/pdf',
		'Content-Disposition'   => 'attachment; filename="file.pdf"'
	    ));
    }
    
    /**
     * @Route("/reports/inventory/rented", name="showRentedInventory")
     * displays rented inventory items
     */
    public function showRentedInventory(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
        $rentedStatus = $em->getRepository('AppBundle:Status')->findOneBy(array('name' => array('RENTED')));
        $inventory = $em->getRepository('AppBundle:Inventory')->findByItemStatus($avalStatus->getId());

        return $this->render('reports/inventory_rented.html.twig', array(
            'items' => $inventory,
        ));
    }
    
    /**
     * @Route("/reports/inventory/rented/pdf", name="createRentedInventoryPDF")
     * generate a pdf displaying rented inventory items
     */
    public function createRentedInventoryPDF(Request $request)
    {
    	    $em = $this->getDoctrine()->getManager();
    	    $rentedStatus = $em->getRepository('AppBundle:Status')->findOneBy(array('name' => array('RENTED')));
            $inventory = $em->getRepository('AppBundle:Inventory')->findByItemStatus($avalStatus->getId());
    	    
	    $html = $this->renderView('reports/inventory_rented.generatepdf.html.twig', array('items' => $inventory));
	
	    return new Response(
	    $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
	    200,
	    array(
		'Content-Type'          => 'application/pdf',
		'Content-Disposition'   => 'attachment; filename="file.pdf"'
	    ));
    }
    
    /**
     * @Route("/reports/inventory/donations", name="showDonationInventory")
     * displays pending donations 
     */
    public function showDonationInventory(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
        $rentedStatus = $em->getRepository('AppBundle:Status')->findOneBy(array('name' => array('PENDING_DONATION')));
        $inventory = $em->getRepository('AppBundle:Inventory')->findByItemStatus($rentedStatus->getId());

        return $this->render('reports/inventory_donations.html.twig', array(
            'items' => $inventory,
        ));
    }
    
    /**
     * @Route("/reports/inventory/donations/pdf", name="createDonationInventoryPDF")
     * generate a pdf displaying pending donations 
     */
    public function createDonationInventoryPDF(Request $request)
    {
    	    $em = $this->getDoctrine()->getManager();
    	    $rentedStatus = $em->getRepository('AppBundle:Status')->findOneBy(array('name' => array('PENDING_DONATION')));
    	    $inventory = $em->getRepository('AppBundle:Inventory')->findByItemStatus($rentedStatus->getId());
    	    
	    $html = $this->renderView('reports/inventory_donations.generatepdf.html.twig', array('items' => $inventory));
	
	    return new Response(
	    $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
	    200,
	    array(
		'Content-Type'          => 'application/pdf',
		'Content-Disposition'   => 'attachment; filename="file.pdf"'
	    ));
    }
    
    /**
     * @Route("/reports/users/donationHistory/{userID}", name="showUserDonationHistory")
     * display donation history of selected user (userID)
     */
    public function showUserDonationHistory(Request $request, $userID = 0)
    {
    	$em = $this->getDoctrine()->getManager();
        $itemStatuses = $em->getRepository('AppBundle:Status')->findby(array('name' => array('AVAL','PENDING_DONATION','RENTED','PENDING_ARRIVAL')));
        $itemStatusIDs = array();
        foreach ($itemStatuses as $Status)
        {
        	$itemStatusIDs[] = $Status->getId();
	}	
	$donations = $em->getRepository('AppBundle:Inventory')->findBy(array(
		'user'=>$userID,
		'itemStatus' => $itemStatusIDs,
	));
	
        return $this->render('reports/users.donationHistory.html.twig', array(
            'user' => $donations,
        ));
    }
    
    /**
     * @Route("/reports/users/donationHistory/pdf/{userID}", name="createUserDonationHistoryPDF")
     * generate a pdf displaying the donation history of selected user (userID
     */
    public function createUserDonationHistoryPDF(Request $request, $userID = 0)
    {
    	    $em = $this->getDoctrine()->getManager();
    	    $itemStatuses = $em->getRepository('AppBundle:Status')->findby(array('name' => array('AVAL','PENDING_DONATION','RENTED','PENDING_ARRIVAL')));
	    $itemStatusIDs = array();
	    foreach ($itemStatuses as $Status)
	    {
	    	    $itemStatusIDs[] = $Status->getId();
	    }	
    	    $donations = $em->getRepository('AppBundle:Inventory')->findBy(array(
		'user'=>$userID,
		'itemStatus' => $itemStatusIDs,
	    ));
    	    
	    $html = $this->renderView('reports/users.donationHistory.generatepdf.html.twig', array('user' => $donations));
	
	    return new Response(
	    $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
	    200,
	    array(
		'Content-Type'          => 'application/pdf',
		'Content-Disposition'   => 'attachment; filename="file.pdf"'
	    ));
    }
    
    /**
     * @Route("/reports/specialRequests", name="showSpecialRequests")
     * displays pending donations 
     */
    public function showSpecialRequests(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
        $requestStatus = $em->getRepository('AppBundle:Status')->findOneBy(array('name' => array('PENDING_SPECIAL')));
        $inventory = $em->getRepository('AppBundle:Inventory')->findByItemStatus($requestStatus->getId());

        return $this->render('reports/specialRequests.html.twig', array(
            'items' => $inventory,
        ));
    }
    
    /**
     * @Route("/reports/specialRequests/pdf", name="createSpecialRequestsPDF")
     * generate a pdf displaying pending donations 
     */
    public function createSpecialRequestsPDF(Request $request)
    {
    	    $em = $this->getDoctrine()->getManager();
    	    $requestStatus = $em->getRepository('AppBundle:Status')->findOneBy(array('name' => array('PENDING_SPECIAL')));
    	    $inventory = $em->getRepository('AppBundle:Inventory')->findByItemStatus($requestStatus->getId());
    	    
	    $html = $this->renderView('reports/specialRequests.generatepdf.html.twig', array('items' => $inventory));
	
	    return new Response(
	    $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
	    200,
	    array(
		'Content-Type'          => 'application/pdf',
		'Content-Disposition'   => 'attachment; filename="file.pdf"'
	    ));
    }
    
    /**
     * @Route("/reports/inventory/pendingArrivals", name="showPendingArrivals")
     * displays pending donations 
     */
    public function showPendingArrivals(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
        $pendingArrivalStatus = $em->getRepository('AppBundle:Status')->findOneBy(array('name' => array('PENDING_ARRIVAL')));
        $inventory = $em->getRepository('AppBundle:Inventory')->findByItemStatus($pendingArrivalStatus->getId());

        return $this->render('reports/inventory_pendingArrivals.html.twig', array(
            'items' => $inventory,
        ));
    }
    
    /**
     * @Route("/reports/inventory/pendingArrivals/pdf", name="createPendingArrivalsPDF")
     * generate a pdf displaying pending donations 
     */
    public function createPendingArrivalsPDF(Request $request)
    {
    	    $em = $this->getDoctrine()->getManager();
    	    $pendingArrivalStatus = $em->getRepository('AppBundle:Status')->findOneBy(array('name' => array('PENDING_ARRIVAL')));
            $inventory = $em->getRepository('AppBundle:Inventory')->findByItemStatus($pendingArrivalStatus->getId());
    	    
	    $html = $this->renderView('reports/inventory_pendingArrivals.generatepdf.html.twig', array('items' => $inventory));
	
	    return new Response(
	    $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
	    200,
	    array(
		'Content-Type'          => 'application/pdf',
		'Content-Disposition'   => 'attachment; filename="file.pdf"'
	    ));
    }
    
    /**
     * @Route("/reports/rentalHistory", name="showRentalHistory")
     * displays rental history 
     */
    public function showRentalHistory(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
        $reservations = $em->getRepository('AppBundle:Reservation')->findAll();

        return $this->render('reports/rentalHistory.html.twig', array(
            'reservations' => $reservations,
        ));
    }
    
    /**
     * @Route("/reports/rentalHistory/pdf", name="createRentalHistoryPDF")
     * generate a pdf displaying rental history 
     */
    public function createRentalHistoryPDF(Request $request)
    {
    	    $em = $this->getDoctrine()->getManager();
    	    $reservations = $em->getRepository('AppBundle:Reservation')->findAll();
    	    
	    $html = $this->renderView('reports/rentalHistory.generatepdf.html.twig', array('reservations' => $reservations));
	
	    return new Response(
	    $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
	    200,
	    array(
		'Content-Type'          => 'application/pdf',
		'Content-Disposition'   => 'attachment; filename="file.pdf"'
	    ));
    }
    
  
}
