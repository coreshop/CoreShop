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

namespace CoreShop\Bundle\CoreBundle\Templating\Helper;

use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use Symfony\Component\Templating\Helper\Helper;

class ConfigurationHelper extends Helper implements ConfigurationHelperInterface
{
    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationService;

    /**
     * @param ConfigurationServiceInterface $configurationService
     */
    public function __construct(ConfigurationServiceInterface $configurationService)
    {
        $this->configurationService = $configurationService;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration($configuration, StoreInterface $store = null)
    {
        return $this->configurationService->getForStore($configuration, $store);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coreshop_product_discount';
    }
}
