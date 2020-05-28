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

namespace CoreShop\Component\Payment\Model;

use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Resource\Model\TimestampableTrait;
use CoreShop\Component\Resource\Model\ToggleableTrait;
use CoreShop\Component\Resource\Model\TranslatableTrait;
use Pimcore\Model\Asset;

class PaymentProvider extends AbstractResource implements PaymentProviderInterface
{
    use TimestampableTrait;
    use ToggleableTrait;
    use TranslatableTrait {
        __construct as initializeTranslationsCollection;
        getTranslation as private doGetTranslation;
    }

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var int
     */
    protected $position = 1;

    /**
     * @var Asset|null
     */
    protected $logo;

    public function __construct()
    {
        $this->initializeTranslationsCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getIdentifier();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle($language = null)
    {
        return $this->getTranslation($language)->getTitle();
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title, $language = null)
    {
        $this->getTranslation($language)->setTitle($title);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription($language = null)
    {
        return $this->getTranslation($language)->getDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description, $language = null)
    {
        $this->getTranslation($language)->setDescription($description);
    }

    /**
     * {@inheritdoc}
     */
    public function getInstructions($language = null)
    {
        return $this->getTranslation($language)->getInstructions();
    }

    /**
     * {@inheritdoc}
     */
    public function setInstructions($instructions, $language = null)
    {
        $this->getTranslation($language)->setInstructions($instructions);
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * {@inheritdoc}
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    /**
     * @param null $locale
     * @param bool $useFallbackTranslation
     *
     * @return PaymentProviderTranslationInterface
     */
    public function getTranslation($locale = null, $useFallbackTranslation = true)
    {
        /** @var PaymentProviderTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale, $useFallbackTranslation);

        return $translation;
    }

    /**
     * {@inheritdoc}
     */
    protected function createTranslation()
    {
        return new PaymentProviderTranslation();
    }

}
