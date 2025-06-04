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

namespace CoreShop\Bundle\PimcoreBundle\Loader;

use CoreShop\Component\Pimcore\Document\DocumentTagFactoryInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Pimcore\Loader\ImplementationLoader\LoaderInterface;
use Pimcore\Model\Document\Editable\EditableInterface;

class DependencyInjectionImplementationLoader implements LoaderInterface
{
    public function __construct(
        private ServiceRegistryInterface $factories,
    ) {
    }

    public function supports(string $name): bool
    {
        return $this->factories->has($name);
    }

    public function build(string $name, array $params = []): EditableInterface
    {
        /**
         * @var DocumentTagFactoryInterface $factory
         */
        $factory = $this->factories->get($name);

        return $factory->create($name, $params);
    }
}
