<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\ResourceBundle\Controller;

use Pimcore\Controller\UserAwareController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Service\Attribute\SubscribedService;

class AdminController extends UserAwareController
{
    public function __construct(
        \Psr\Container\ContainerInterface $container,
        protected ViewHandlerInterface $viewHandler,
        protected ParameterBagInterface $parameterBag,
    ) {
        $this->container = $container;
    }

    protected function getParameter(string $name): array|bool|string|int|float|\UnitEnum|null
    {
        return $this->parameterBag->get($name);
    }

    protected function getParameterFromRequest(Request $request, string $key, $default = null): mixed
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

    /**
     * @psalm-suppress ImplementedReturnTypeMismatch
     *
     * @return array<array-key, SubscribedService|string>
     */
    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices();
    }
}
