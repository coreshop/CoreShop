<?php

namespace CoreShop\Bundle\CoreBundle\NumberGenerator;

use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Sequence\Generator\SequenceGeneratorInterface;

class OrderShipmentNumberGenerator extends SequenceNumberGenerator
{
    /**
     * @var ConfigurationServiceInterface
     */
    protected $configurationService;

    /**
     * @param SequenceGeneratorInterface $sequenceNumberGenerator
     * @param string $type
     * @param ConfigurationServiceInterface $configurationService
     */
    public function __construct(SequenceGeneratorInterface $sequenceNumberGenerator, $type, ConfigurationServiceInterface $configurationService)
    {
        parent::__construct($sequenceNumberGenerator, $type);

        $this->configurationService = $configurationService;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ResourceInterface $model)
    {
        $number = parent::generate($model);

        return sprintf('%s%s%s', $this->configurationService->getForStore('system.shipment.prefix'), $number, $this->configurationService->getForStore('system.shipment.suffix'));
    }
}