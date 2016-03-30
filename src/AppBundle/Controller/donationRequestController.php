<?php
/**
 * Created by PhpStorm.
 * User: mohammedkashkari
 * Date: 3/20/16
 * Time: 10:24 PM
 */
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use AppBundle\Entity\Inventory;
use AppBundle\Entity\Status;




class donationRequestController extends Controller
{
    /**
     * @Route("/donation_request", name="donationRequest")
     */
    public function indexAction(Request $request)
    {
        $donationRequest = new Inventory();

        $form = $this->createFormBuilder($donationRequest)
            ->add('itemDescription', TextType::class)
            ->add('itemLength', IntegerType::class)
            ->add('itemWidth', IntegerType::class)
            ->add('itemType')
            ->add('itemColor')
            ->add('itemSchool')
            ->add('itemMajor')
            ->add('save', SubmitType::class, array('label' => 'Submit your donation request'))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            # The "donation Request" status has an id = 4;
            $Status = $this->getDoctrine()->getRepository('AppBundle:Status')->find(4);
            $donationRequest->setItemStatus($Status);
            $User = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('username'  => $this->getUser()));
            $donationRequest->setUser($User);
            $em = $this->getDoctrine()->getManager();
            $em->persist($donationRequest);
            $em->flush();

            $message = \Swift_Message::newInstance()
                ->setSubject('New Donation Request Received')
                ->setFrom('wuregalia@gmail.com')
                ->setTo('wuregalia@gmail.com')
                ->setBody(
                    $this->renderView(
                        'emailsNotifications/donationRequest/adminNewDonationRequest.txt.twig'
                    ),
                    'text/html'
                );
            $this->get('mailer')->send($message);

            $message = \Swift_Message::newInstance()
                ->setSubject('Donation Request Received')
                ->setFrom('wuregalia@gmail.com')
                ->setTo('wuregalia@gmail.com')
                ->setBody(
                    $this->renderView(
                        'emailsNotifications/donationRequest/userDonationRequestReceived.txt.twig'
                    ),
                    'text/html'
                );
            $this->get('mailer')->send($message);

            return $this->render('donationRequest/success.html.twig');
        }

        return $this->render('donationRequest/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    /**
     * @Route("/admin/donation_request", name="adminDonationRequest")
     */
    public function adminAction(Request $request)
    {
        $donationRequestsList = $this->getDoctrine()->getRepository('AppBundle:Inventory')->findBy(array('itemStatus' => '4' ));

        return $this->render('donationRequest/admin.html.twig',array(
            'donationRequests' => $donationRequestsList
        ));
    }
    /**
     * @Route("/admin/donation_request/status/{inventoryRequest}/{newStatus}", name="acceptDonationRequest")
     */
    public function adminChangeStatusAction(Request $request, $inventoryRequest,  $newStatus)
    {
        $em = $this->getDoctrine()->getManager();
        $record = $em->getRepository('AppBundle:Inventory')->find($inventoryRequest);
        $Status = $em->getRepository('AppBundle:Status')->find($newStatus);
        $record->setItemStatus($Status);
        $em->flush();

        $message = \Swift_Message::newInstance()
            ->setSubject('Donation Request Accepted')
            ->setFrom('wuregalia@gmail.com')
            ->setTo('wuregalia@gmail.com')
            ->setBody(
                $this->renderView(
                    'emailsNotifications/donationRequest/userDonationRequestAccepted.txt.twig'
                ),
                'text/html'
            );
        $this->get('mailer')->send($message);

        $donationRequestsList = $this->getDoctrine()->getRepository('AppBundle:Inventory')->findBy(array('itemStatus' => '4' ));
        return $this->render('donationRequest/admin.html.twig',array(
            'donationRequests' => $donationRequestsList));
    }


    /**
     * @Route("/admin/donation_request/reject/{inventoryRequest}", name="rejectDonationRequest")
     */
    public function adminRejectSpecialRequestAction(Request $request,$inventoryRequest)
    {
        $em = $this->getDoctrine()->getManager();
        $record = $em->getRepository('AppBundle:Inventory')->find($inventoryRequest);
        $em->remove($record);
        $em->flush();

        $message = \Swift_Message::newInstance()
            ->setSubject('Donation Request Rejected')
            ->setFrom('wuregalia@gmail.com')
            ->setTo('wuregalia@gmail.com')
            ->setBody(
                $this->renderView(
                    'emailsNotifications/donationRequest/userDonationRequestRejected.txt.twig'
                ),
                'text/html'
            );

        $this->get('mailer')->send($message);
        $donationRequestsList = $this->getDoctrine()->getRepository('AppBundle:Inventory')->findBy(array('itemStatus' => '4' ));
        return $this->render('donationRequest/admin.html.twig',array(
            'donationRequests' => $donationRequestsList));
    }

}
