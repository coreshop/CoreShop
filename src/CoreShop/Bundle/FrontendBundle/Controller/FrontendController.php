<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\FrontendBundle\TemplateConfigurator\TemplateConfiguratorInterface;
use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\SEO\SEOPresentationInterface;
use Pimcore\Http\RequestHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class FrontendController extends AbstractController
{
    public function __construct(
        \Psr\Container\ContainerInterface $container,
    ) {
        $this->container = $container;
    }

    protected function getTemplateConfigurator(): TemplateConfiguratorInterface
    {
        return $this->container->get(TemplateConfiguratorInterface::class);
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            TemplateConfiguratorInterface::class => TemplateConfiguratorInterface::class,
            ShopperContextInterface::class => ShopperContextInterface::class,
            CartContextInterface::class => CartContextInterface::class,
            'translator' => TranslatorInterface::class,
            RequestHelper::class => RequestHelper::class,
            SEOPresentationInterface::class => SEOPresentationInterface::class,
        ]);
    }

    /**
     * @return mixed
     *
     * based on Symfony\Component\HttpFoundation\Request::get
     */
    protected function getParameterFromRequest(Request $request, string $key, $default = null)
    {
        if ($request !== $result = $request->attributes->get($key, $request)) {
            return $result;
        }

        if ($request->query->has($key)) {
            return $request->query->all()[$key];
        }

        if ($request->request->has($key)) {
            return $request->request->all()[$key];
        }

        return $default;
    }
}
