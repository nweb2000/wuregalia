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



class specialRequestController extends Controller
{
    /**
     * @Route("/special_request", name="specialRequest")
     */
    public function indexAction(Request $request)
    {
        $specialRequest = new Inventory();

        $form = $this->createFormBuilder($specialRequest)
            ->add('itemDescription', TextType::class)
            ->add('itemLength', IntegerType::class)
            ->add('itemWidth', IntegerType::class)
            ->add('itemType')
            ->add('itemColor')
            ->add('itemSchool')
            ->add('itemMajor')
            ->add('save', SubmitType::class, array('label' => 'Submit your special request'))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            # The "Special Request" status has an id = 1;
            $Status = $this->getDoctrine()->getRepository('AppBundle:Status')->find(1);
            $specialRequest->setItemStatus($Status);
            $User = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('username'  => $this->getUser()));
            $specialRequest->setUser($User);
            $em = $this->getDoctrine()->getManager();
            $em->persist($specialRequest);
            $em->flush();

            $message = \Swift_Message::newInstance()
                ->setSubject('New Special Request Received')
                ->setFrom('wuregalia@gmail.com')
                ->setTo('wuregalia@gmail.com')
                ->setBody(
                    $this->renderView(
                        'emailsNotifications/specialRequest/adminNewSpecialRequest.txt.twig'
                    ),
                    'text/html'
                );
            $this->get('mailer')->send($message);

            $message = \Swift_Message::newInstance()
                ->setSubject('Special Request Received')
                ->setFrom('wuregalia@gmail.com')
                ->setTo('wuregalia@gmail.com')
                ->setBody(
                    $this->renderView(
                        'emailsNotifications/specialRequest/userSpecialRequestReceived.txt.twig'
                    ),
                    'text/html'
                );
            $this->get('mailer')->send($message);

            return $this->render('specialRequest/success.html.twig');
        }

        return $this->render('specialRequest/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    /**
     * @Route("/admin/special_request", name="adminSpecialRequest")
     */
    public function adminAction(Request $request)
    {
        $specialRequestsList = $this->getDoctrine()->getRepository('AppBundle:Inventory')->findBy(array('itemStatus' => '1' ));
        return $this->render('specialRequest/admin.html.twig',array(
            'specialRequests' => $specialRequestsList
        ));
    }


    /**
     * @Route("/admin/special_request/status/{inventoryRequest}/{newStatus}", name="acceptSpecialRequest")
     */
    public function adminChangeStatusAction(Request $request, $inventoryRequest,  $newStatus)
    {
        $em = $this->getDoctrine()->getManager();
        $record = $em->getRepository('AppBundle:Inventory')->find($inventoryRequest);
        $Status = $em->getRepository('AppBundle:Status')->find($newStatus);
        $record->setItemStatus($Status);
        $em->flush();

        $message = \Swift_Message::newInstance()
            ->setSubject('Special Request Accepted')
            ->setFrom('wuregalia@gmail.com')
            ->setTo('wuregalia@gmail.com')
            ->setBody(
                $this->renderView(
                    'emailsNotifications/specialRequest/userSpecialRequestAccepted.txt.twig'
                ),
                'text/html'
            );
        $this->get('mailer')->send($message);

        $specialRequestsList = $this->getDoctrine()->getRepository('AppBundle:Inventory')->findBy(array('itemStatus' => '1' ));
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
                    'emailsNotifications/specialRequest/userSpecialRequestRejected.txt.twig'
                ),
                'text/html'
            );

        $this->get('mailer')->send($message);
        $specialRequestsList = $this->getDoctrine()->getRepository('AppBundle:Inventory')->findBy(array('itemStatus' => '1' ));
        return $this->render('specialRequest/admin.html.twig',array(
            'specialRequests' => $specialRequestsList));
    }

}
