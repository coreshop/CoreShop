<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\LocaleBundle\DependencyInjection;

use CoreShop\Bundle\LocaleBundle\DependencyInjection\Compiler\CompositeLocaleContextPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class CoreShopLocaleExtension extends AbstractModelExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $container
            ->registerForAutoconfiguration(LocaleContextInterface::class)
            ->addTag(CompositeLocaleContextPass::LOCALE_CONTEXT_SERVICE_TAG);
    }
}
