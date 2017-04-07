<?php

namespace CoreShop\Bundle\FrontendBundle\EventListener;

use CoreShop\Bundle\ResourceBundle\Controller\PimcoreFrontendController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class ThemeSetListener
{
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof PimcoreFrontendController) {
            $activeTheme = $controller[0]->container->get('liip_theme.active_theme');
            $activeTheme->setName('default2');
        }
    }
}
