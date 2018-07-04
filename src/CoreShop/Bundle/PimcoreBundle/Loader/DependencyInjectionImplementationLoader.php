<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\PimcoreBundle\Loader;

use CoreShop\Component\Registry\ServiceRegistryInterface;
use Pimcore\Loader\ImplementationLoader\LoaderInterface;

class DependencyInjectionImplementationLoader implements LoaderInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $factories;

    /**
     * @param ServiceRegistryInterface $factories
     */
    public function __construct(ServiceRegistryInterface $factories)
    {
        $this->factories = $factories;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $name): bool
    {
        return $this->factories->has($name);
    }

    /**
     * {@inheritdoc}
     */
    public function build(string $name, array $params = [])
    {
        $factory = $this->factories->get($name);

        return $factory->create($name, $params);
    }
}
