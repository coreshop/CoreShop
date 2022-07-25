<?php

namespace CoreShop\Bundle\ElasticsearchBundle\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends FrontendController
{
    /**
     * @Route("/elasticsearch")
     */
    public function indexAction(Request $request)
    {
        return new Response('Hello world from elasticsearch');
    }
}
