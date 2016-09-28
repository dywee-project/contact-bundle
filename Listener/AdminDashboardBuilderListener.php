<?php

namespace Dywee\ContactBundle\Listener;

use Dywee\CoreBundle\DyweeCoreEvent;
use Dywee\CoreBundle\Event\AdminDashboardBuilderEvent;
use Dywee\OrderBundle\Service\OrderAdminDashboardHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class AdminDashboardBuilderListener implements EventSubscriberInterface{
    private $orderAdminDashboardHandler;

    public function __construct(OrderAdminDashboardHandler $orderAdminDashboardHandler)
    {
        $this->orderAdminDashboardHandler = $orderAdminDashboardHandler;
    }

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return array(
            DyweeCoreEvent::BUILD_ADMIN_DASHBOARD => array('addElementToDashboard', 2048)
        );
    }

    public function addElementToDashboard(AdminDashboardBuilderEvent $adminDashboardBuilderEvent)
    {
        $adminDashboardBuilderEvent->addElement($this->orderAdminDashboardHandler->getDashboardElement());
    }

}