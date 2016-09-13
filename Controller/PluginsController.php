<?php
namespace Dywee\ContactBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PluginsController extends Controller
{
    public function ContactInfosAction()
    {
        $data = array();
        return $this->render('DyweeContactBundle:Plugins:contactInfos.html.twig', $data);
    }
}
