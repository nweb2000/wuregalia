<?php
/**
 * Created by PhpStorm.
 * User: mohammedkashkari
 * Date: 3/20/16
 * Time: 10:24 PM
 */
namespace AppBundle\Controller;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
            ->add('itemType')
            ->add('itemColor')
            ->add('itemSchool')
            ->add('itemMajor')
            ->add('itemSize', TextType::class,  array('attr' => array('placeholder' => "Example : 5'8'' ")))
            ->add('itemDescription',TextareaType::class , array('attr' => array('cols' => '70', 'rows' => '5','placeholder' => 'Please specify the condition of the item'), ))
            ->add('save', SubmitType::class, array('label' => 'Submit your donation request'))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $Status = $this->getDoctrine()->getRepository('AppBundle:Status')->findOneBy(array('name'  => 'PENDING_DONATION'));
            $donationRequest->setItemStatus($Status);
            $User = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('username'  => $this->getUser()));
            $donationRequest->setUser($User);

            $em = $this->getDoctrine()->getManager();
            $em->persist($donationRequest);
            $em->flush();

            $message = \Swift_Message::newInstance()
                ->setSubject('New Donation Request Submitted to WU Regalia Closet')
                ->setFrom('wuregalia@gmail.com')
                ->setTo('wuregalia@gmail.com')
                ->setBody(
                    $this->renderView(
                        'emailsNotifications/donationRequest/adminNewDonationRequest.txt.twig',array('donationRequest' => $donationRequest)
                    ),
                    'text/html'
                );
            $this->get('mailer')->send($message);

            $message = \Swift_Message::newInstance()
                ->setSubject('Donation Request Confirmation')
                ->setFrom('wuregalia@gmail.com')
                ->setTo('wuregalia@gmail.com')
                ->setBody(
                    $this->renderView(
                        'emailsNotifications/donationRequest/userDonationRequestReceived.txt.twig', array('donationRequest' => $donationRequest)
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
        $Status = $this->getDoctrine()->getRepository('AppBundle:Status')->findOneBy(array('name'  => 'PENDING_DONATION'));
        $donationRequestsList = $this->getDoctrine()->getRepository('AppBundle:Inventory')->findBy(array('itemStatus' => $Status ));

        return $this->render('donationRequest/admin.html.twig',array(
            'donationRequests' => $donationRequestsList
        ));
    }
    /**
     * @Route("/admin/donation_request/status/{inventoryRequest}", name="acceptDonationRequest")
     */
    public function adminChangeStatusAction(Request $request, $inventoryRequest)
    {
        $em = $this->getDoctrine()->getManager();
        $record = $em->getRepository('AppBundle:Inventory')->find($inventoryRequest);
        $Status = $em->getRepository('AppBundle:Status')->findOneBy(array('name' => 'PENDING_ARRIVAL' ));
        $record->setItemStatus($Status);

        $em->flush();

        $message = \Swift_Message::newInstance()
            ->setSubject('Donation Request Accepted')
            ->setFrom('wuregalia@gmail.com')
            ->setTo('wuregalia@gmail.com')
            ->setBody(
                $this->renderView(
                    'emailsNotifications/donationRequest/userDonationRequestAccepted.txt.twig', array('donationRequest' => $record)
                ),
                'text/html'
            );
        $this->get('mailer')->send($message);

        $Status = $this->getDoctrine()->getRepository('AppBundle:Status')->findOneBy(array('name'  => 'PENDING_DONATION'));
        $donationRequestsList = $this->getDoctrine()->getRepository('AppBundle:Inventory')->findBy(array('itemStatus' => $Status ));
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
                    'emailsNotifications/donationRequest/userDonationRequestRejected.txt.twig', array('donationRequest' => $record)
                ),
                'text/html'
            );
        $this->get('mailer')->send($message);

        $Status = $this->getDoctrine()->getRepository('AppBundle:Status')->findOneBy(array('name'  => 'PENDING_DONATION'));
        $donationRequestsList = $this->getDoctrine()->getRepository('AppBundle:Inventory')->findBy(array('itemStatus' => $Status ));
        return $this->render('donationRequest/admin.html.twig',array(
            'donationRequests' => $donationRequestsList));
    }

}
