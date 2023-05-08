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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\StoreBundle\CoreExtension;

use CoreShop\Bundle\ResourceBundle\CoreExtension\Select;
use CoreShop\Component\Store\Model\StoreInterface;
use InvalidArgumentException;
use Pimcore\Model\DataObject\ClassDefinition\Helper\OptionsProviderResolver;

/**
 * @psalm-suppress InvalidReturnType, InvalidReturnStatement
 */
class Store extends Select
{
    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopStore';

    /** @var array|null */
    public $options = [];

    /** @var string */
    public $optionsProviderClass = '@' . StoreOptionProvider::class;

    protected function getRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.store');
    }

    protected function getModel(): string
    {
        return \Pimcore::getContainer()->getParameter('coreshop.model.store.class');
    }

    protected function getInterface(): string
    {
        return '\\' . StoreInterface::class;
    }

    protected function getNullable(): bool
    {
        return true;
    }

    public function getOptionsProviderClass()
    {
        return $this->optionsProviderClass;
    }

    public function getDataForGrid($data, $object = null, $params = [])
    {
        $optionsProvider = OptionsProviderResolver::resolveProvider(
            $this->getOptionsProviderClass(),
            OptionsProviderResolver::MODE_SELECT,
        );

        if ($optionsProvider) {
            $context = $params['context'] ?? [];
            $context['object'] = $object;
            if ($object) {
                $context['class'] = $object->getClass();
            }

            $context['fieldname'] = $this->getName();
            $options = $optionsProvider->{'getOptions'}($context, $this);
            $this->setOptions($options);

            if (isset($params['purpose']) && $params['purpose'] === 'editmode') {
                $result = $data?->getId();
            } else {
                $result = ['value' => $data?->getId(), 'options' => $this->getOptions()];
            }

            return $result;
        }

        return $data?->getId();
    }

    /**
     * @return $this
     */
    public function setOptions(?array $options)
    {
        if (is_array($options)) {
            $this->options = [];
            foreach ($options as $option) {
                $option = (array) $option;
                if (!array_key_exists('key', $option) || !array_key_exists('value', $option)) {
                    throw new InvalidArgumentException('Please provide select options as associative array with fields "key" and "value"');
                }

                $this->options[] = $option;
            }
        } else {
            $this->options = null;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
