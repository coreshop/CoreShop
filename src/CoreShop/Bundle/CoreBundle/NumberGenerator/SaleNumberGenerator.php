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

namespace CoreShop\Bundle\CoreBundle\NumberGenerator;

use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use CoreShop\Component\Order\NumberGenerator\NumberGeneratorInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Sequence\Generator\SequenceGeneratorInterface;

final class SaleNumberGenerator implements NumberGeneratorInterface
{
    /**
     * @var NumberGeneratorInterface
     */
    protected $numberGenerator;

    /**
     * @var ConfigurationServiceInterface
     */
    protected $configurationService;

    /**
     * @var string
     */
    protected $prefixConfigurationKey;

    /**
     * @var string
     */
    protected $suffixConfigurationKey;

    /**
     * @param NumberGeneratorInterface $numberGenerator
     * @param ConfigurationServiceInterface $configurationService
     * @param string $prefixConfigurationKey
     * @param string $suffixConfigurationKey
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
        return sprintf('%s%s%s', $this->configurationService->getForStore($this->prefixConfigurationKey), $this->numberGenerator->generate($model), $this->configurationService->getForStore($this->suffixConfigurationKey));
    }
}
