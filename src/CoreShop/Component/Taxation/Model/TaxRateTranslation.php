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

namespace CoreShop\Component\Taxation\Model;

use CoreShop\Component\Resource\Model\AbstractTranslation;
use CoreShop\Component\Resource\Model\TimestampableTrait;

/**
 * @psalm-suppress MissingConstructor
 */
class TaxRateTranslation extends AbstractTranslation implements TaxRateTranslationInterface
{
    use TimestampableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
