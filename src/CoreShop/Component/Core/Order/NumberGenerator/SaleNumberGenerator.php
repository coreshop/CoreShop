<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Order\NumberGenerator;

use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\SaleInterface;
use CoreShop\Component\Order\NumberGenerator\NumberGeneratorInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Store\Model\StoreAwareInterface;

final class SaleNumberGenerator implements NumberGeneratorInterface
{
    /**
     * @var NumberGeneratorInterface
     */
    private $numberGenerator;

    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationService;

    /**
     * @var string
     */
    private $prefixConfigurationKey;

    /**
     * @var string
     */
    private $suffixConfigurationKey;

    /**
     * @param NumberGeneratorInterface      $numberGenerator
     * @param ConfigurationServiceInterface $configurationService
     * @param string                        $prefixConfigurationKey
     * @param string                        $suffixConfigurationKey
     */
    public function __construct(NumberGeneratorInterface $numberGenerator, ConfigurationServiceInterface $configurationService, $prefixConfigurationKey, $suffixConfigurationKey)
    {
        $this->numberGenerator = $numberGenerator;
        $this->configurationService = $configurationService;
        $this->prefixConfigurationKey = $prefixConfigurationKey;
        $this->suffixConfigurationKey = $suffixConfigurationKey;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ResourceInterface $model)
    {
        $store = null;

        if ($model instanceof SaleInterface) {
            $store = $model->getStore();
        } elseif ($model instanceof OrderDocumentInterface) {
            $store = $model->getOrder()->getStore();
        } elseif ($model instanceof StoreAwareInterface) {
            $store = $model->getStore();
        }

        if ($store instanceof StoreInterface) {
            $prefix = $this->configurationService->getForStore($this->prefixConfigurationKey, $store);
            $suffix = $this->configurationService->getForStore($this->suffixConfigurationKey, $store);
            
            //Implement some useful replacements
            $prefix = str_replace('{date}',date('Ymd'),$prefix);
            $prefix = str_replace('{year}',date('Y'),$prefix);
            $prefix = str_replace('{month}',date('m'),$prefix);
            $prefix = str_replace('{day}',date('d'),$prefix);
            
            //ToDo: Implement the same replacements for $suffix (use same function for both)
            
            return sprintf('%s%s%s', $prefix, $this->numberGenerator->generate($model), $suffix);
        }

        return $this->numberGenerator->generate($model);
    }
}
