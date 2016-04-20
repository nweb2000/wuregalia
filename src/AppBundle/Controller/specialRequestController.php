<?php
/**
 * Created by PhpStorm.
 * User: mohammedkashkari
 * Date: 3/20/16
 * Time: 10:24 PM
 */
namespace AppBundle\Controller;

use AppBundle\Entity\User;
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



class specialRequestController extends Controller
{
    /**
     * @Route("/special_request", name="specialRequest")
     */
    public function indexAction(Request $request)
    {
        $specialRequest = new Inventory();

        $form = $this->createFormBuilder($specialRequest)
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
            $Status = $this->getDoctrine()->getRepository('AppBundle:Status')->findOneBy(array('name'  => 'PENDING_SPECIAL'));
            $specialRequest->setItemStatus($Status);
            $User = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('username'  => $this->getUser()));
            $specialRequest->setUser($User);

            $em = $this->getDoctrine()->getManager();
            $em->persist($specialRequest);
            $em->flush();

            $message = \Swift_Message::newInstance()
                ->setSubject('New Special Request Submitted to WU Regalia Closet')
                ->setFrom('wuregalia@gmail.com')
                ->setTo('wuregalia@gmail.com')
                ->setBody(
                    $this->renderView(
                        'emailsNotifications/specialRequest/adminNewSpecialRequest.txt.twig', array('specialRequest' => $specialRequest)
                    ),
                    'text/html'
                );
            $this->get('mailer')->send($message);

            $message = \Swift_Message::newInstance()
                ->setSubject('Special Request Confirmation')
                ->setFrom('wuregalia@gmail.com')
                ->setTo('wuregalia@gmail.com')
                ->setBody(
                    $this->renderView(
                        'emailsNotifications/specialRequest/userSpecialRequestReceived.txt.twig', array('specialRequest' => $specialRequest)
                    ),
                    'text/html'
                );
            $this->get('mailer')->send($message);

            return $this->render('specialRequest/success.html.twig');
        }

        return $this->render('specialRequest/new.html.twig', array(
            'form' => $form->createView()
        ));
    }
    /**
     * @Route("/admin/special_request", name="adminSpecialRequest")
     */
    public function adminAction(Request $request)
    {
        $Status = $this->getDoctrine()->getRepository('AppBundle:Status')->findOneBy(array('name'  => 'PENDING_SPECIAL'));
        $specialRequestsList = $this->getDoctrine()->getRepository('AppBundle:Inventory')->findBy(array('itemStatus' => $Status ));
        return $this->render('specialRequest/admin.html.twig',array(
            'specialRequests' => $specialRequestsList
        ));
    }


    /**
     * @Route("/admin/special_request/status/{inventoryRequest}", name="acceptSpecialRequest")
     */
    public function adminChangeStatusAction(Request $request, $inventoryRequest)
    {
        $em = $this->getDoctrine()->getManager();
        $record = $em->getRepository('AppBundle:Inventory')->find($inventoryRequest);
        $Status = $em->getRepository('AppBundle:Status')->findOneBy(array('name'  => 'PENDING_ARRIVAL'));
        $record->setItemStatus($Status);
        $em->flush();

        $message = \Swift_Message::newInstance()
            ->setSubject('Special Request Accepted')
            ->setFrom('wuregalia@gmail.com')
            ->setTo('wuregalia@gmail.com')
            ->setBody(
                $this->renderView(
                    'emailsNotifications/specialRequest/userSpecialRequestAccepted.txt.twig', array('specialRequest' => $record)
                ),
                'text/html'
            );
        $this->get('mailer')->send($message);

        $Status = $this->getDoctrine()->getRepository('AppBundle:Status')->findOneBy(array('name'  => 'PENDING_SPECIAL'));
        $specialRequestsList = $this->getDoctrine()->getRepository('AppBundle:Inventory')->findBy(array('itemStatus' => $Status ));
        return $this->render('specialRequest/admin.html.twig',array(
            'specialRequests' => $specialRequestsList));
    }


    /**
     * @Route("/admin/special_request/reject/{inventoryRequest}", name="rejectSpecialRequest")
     */
    public function adminRejectSpecialRequestAction(Request $request,$inventoryRequest)
    {
        $em = $this->getDoctrine()->getManager();
        $record = $em->getRepository('AppBundle:Inventory')->find($inventoryRequest);
        $em->remove($record);
        $em->flush();

        $message = \Swift_Message::newInstance()
            ->setSubject('Special Request Rejected')
            ->setFrom('wuregalia@gmail.com')
            ->setTo('wuregalia@gmail.com')
            ->setBody(
                $this->renderView(
                    'emailsNotifications/specialRequest/userSpecialRequestRejected.txt.twig', array('specialRequest' => $record)
                ),
                'text/html'
            );
        $this->get('mailer')->send($message);

        $Status = $this->getDoctrine()->getRepository('AppBundle:Status')->findOneBy(array('name'  => 'PENDING_SPECIAL'));
        $specialRequestsList = $this->getDoctrine()->getRepository('AppBundle:Inventory')->findBy(array('itemStatus' => $Status ));
        return $this->render('specialRequest/admin.html.twig',array(
            'specialRequests' => $specialRequestsList));
    }

}
