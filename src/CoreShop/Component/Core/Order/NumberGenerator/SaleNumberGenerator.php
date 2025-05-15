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

namespace CoreShop\Component\Core\Order\NumberGenerator;

use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\NumberGenerator\NumberGeneratorInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Store\Model\StoreAwareInterface;

final class SaleNumberGenerator implements NumberGeneratorInterface
{
    public function __construct(
        private NumberGeneratorInterface $numberGenerator,
        private ConfigurationServiceInterface $configurationService,
        private string $prefixConfigurationKey,
        private string $suffixConfigurationKey,
    ) {
    }

    public function generate(ResourceInterface $model): string
    {
        $store = null;

        if ($model instanceof OrderInterface) {
            $store = $model->getStore();
        } elseif ($model instanceof OrderDocumentInterface) {
            $store = $model->getOrder()->getStore();
        } elseif ($model instanceof StoreAwareInterface) {
            $store = $model->getStore();
        }

        if ($store instanceof StoreInterface) {
            return sprintf('%s%s%s', $this->configurationService->getForStore($this->prefixConfigurationKey, $store), $this->numberGenerator->generate($model), $this->configurationService->getForStore($this->suffixConfigurationKey, $store));
        }

        return $this->numberGenerator->generate($model);
    }
}
