<?php

namespace Dywee\ContactBundle\Controller;

use Dywee\CMSBundle\Entity\Page;
use Dywee\ContactBundle\Entity\Message;
use Dywee\ContactBundle\Form\MessageType;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ContactController extends AbstractController
{
    /**
     * @Route(name="dywee_message_new", path="/contact", methods={"POST", "GET"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $message = new Message();

        $form = $this->get('form.factory')->create(MessageType::class, $message);
        $data = ['form' => $form->createView()];

        $em = $this->getDoctrine()->getManager();
        $pr = $em->getRepository(Page::class);
        $page = $pr->findOneByType(3);

        if ($page) {
            $data['page'] = $page;
        }

        if ($form->handleRequest($request)->isValid()) {
            $em->persist($message);
            $em->flush();

            $message = \Swift_Message::newInstance()
                ->setSubject('Nouveau message reçu sur votre site')
                ->setFrom('contact@dywee.com')
                ->setTo($this->container->getParameter('webmaster.email'))
                ->setBody('Nouveau message reçu sur l\'administration de votre site');
            $message->setContentType("text/html");

            $this->get('mailer')->send($message);

            $request->getSession()->getFlashBag()->add('success', 'Votre message a bien été envoyé');

            return $this->render('DyweeContactBundle:Message:sended.html.twig');
        }

        return $this->render('DyweeContactBundle:Message:add.html.twig', $data);
    }

    /**
     * @param null $forcedWebsite
     *
     * @return Response
     */
    public function dropdownAction($forcedWebsite = null)
    {
        $em = $this->getDoctrine()->getManager();
        $mr = $em->getRepository('DyweeContactBundle:Message');

        if ($forcedWebsite) {
            $messageList = $mr->findForDropdown($forcedWebsite);
        } else {
            $messageList = $mr->findForDropdown($this->get('session')->get('activeWebsite'));
        }

        return $this->render('DyweeContactBundle:Message:adminNavbar.html.twig', ['messageList' => $messageList]);
    }

    /**
     * @return Response
     */
    public function tableAction()
    {
        $em = $this->getDoctrine()->getManager();
        $mr = $em->getRepository('DyweeContactBundle:Message');
        $messages = $mr->findBy(
            [],
            ['sendAt' => 'desc']
        );

        return $this->render('DyweeContactBundle:Message:table.html.twig', ['messages' => $messages]);
    }

    /**
     * @param Message $message
     *
     * @return Response
     */
    public function viewAction(Message $message)
    {
        $em = $this->getDoctrine()->getManager();

        $message->setStatus(1);
        $em->persist($message);
        $em->flush();

        return $this->render('DyweeContactBundle:Message:view.html.twig', ['message' => $message]);
    }

    /**
     * @param Message $message
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Message $message)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($message);
        $em->flush();
        $this->get('session')->getFlashBag()->set('success', 'Message bien supprimé');

        return $this->redirect($this->generateUrl('dywee_message_table'));
    }
}
