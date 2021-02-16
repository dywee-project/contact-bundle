<?php

namespace Dywee\ContactBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PluginsController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ContactInfosAction()
    {
        $data = [];

        return $this->render('DyweeContactBundle:Plugins:contactInfos.html.twig', $data);
    }
}
