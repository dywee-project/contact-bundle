<?php

namespace Dywee\ContactBundle\Controller;

use Dywee\ContactBundle\Entity\Message;
use Dywee\ContactBundle\Form\MessageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ContactController extends Controller
{
    public function newAction(Request $request)
    {
        $message = new Message();

        $form = $this->get('form.factory')->create(MessageType::class, $message);
        $data = array('form' => $form->createView());

        $em = $this->getDoctrine()->getManager();
        $pr = $em->getRepository('DyweeCMSBundle:Page');
        $page = $pr->findOneByType(3);

        if($page)
            $data['page'] = $page;

        if($form->handleRequest($request)->isValid())
        {
            $websiteRepository = $em->getRepository('DyweeWebsiteBundle:Website');
            $website = $websiteRepository->findOneById($this->container->getParameter('website.id'));
            $em->persist($message);
            $em->flush();

            $message = \Swift_Message::newInstance()
                ->setSubject('Nouveau message reçu sur votre site '.$website->getName())
                ->setFrom('contact@dywee.com')
                ->setTo($this->container->getParameter('webmaster.email'))
                ->setBody('Nouveau message reçu sur l\'administration de '.$website->getName());
            ;
            $message->setContentType("text/html");

            $this->get('mailer')->send($message);

            $request->getSession()->getFlashBag()->add('success', 'Votre message a bien été envoyé');

            return $this->render('DyweeContactBundle:Message:sended.html.twig');
        }

        return $this->render('DyweeContactBundle:Message:add.html.twig', $data);
    }

    public function dropdownAction($forcedWebsite = null)
    {
        $em = $this->getDoctrine()->getManager();
        $mr = $em->getRepository('DyweeContactBundle:Message');

        if($forcedWebsite)
        {
            $messageList = $mr->findForDropdown($forcedWebsite);
        }
        else{
            //$websiteRepository = $em->getRepository('DyweeWebsiteBundle:Website');
            $messageList = $mr->findForDropdown($this->get('session')->get('activeWebsite'));
        }

        return $this->render('DyweeContactBundle:Message:adminNavbar.html.twig', array('messageList' => $messageList));
    }

    public function tableAction()
    {
        $em = $this->getDoctrine()->getManager();
        $mr = $em->getRepository('DyweeContactBundle:Message');
        $messages = $mr->findBy(
            array(),
            array('sendAt' => 'desc')
        );
        return $this->render('DyweeContactBundle:Message:table.html.twig', array('messages' => $messages));
    }

    public function viewAction(Message $message)
    {
        $em = $this->getDoctrine()->getManager();

        $message->setStatus(1);
        $em->persist($message);
        $em->flush();
        return $this->render('DyweeContactBundle:Message:view.html.twig', array('message' => $message));
    }

    public function deleteAction(Message $message)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($message);
        $em->flush();
        $this->get('session')->getFlashBag()->set('success', 'Message bien supprimé');
        return $this->redirect($this->generateUrl('dywee_message_table'));
    }
}