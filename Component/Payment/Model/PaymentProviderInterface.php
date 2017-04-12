<?php

namespace CoreShop\Component\Payment\Model;

use CoreShop\Component\Resource\Model\ToggleableInterface;
use CoreShop\Component\Resource\Model\TranslatableInterface;

interface PaymentProviderInterface extends
    PaymentProviderTranslationInterface,
    ToggleableInterface,
    TranslatableInterface
{
    /**
     * @return mixed
     */
    public function getIdentifier();

    /**
     * @param null $language
     */
    public function setIdentifier($identifier);

    /**
     * @param null $language
     * @return string
     */
    public function getName($language = null);

    /**
     * @param string $name
     * @param null $language
     */
    public function setName($name, $language = null);

    /**
     * @param null $language
     * @return string
     */
    public function getDescription($language = null);

    /**
     * @param string $description
     * @param null $language
     */
    public function setDescription($description, $language = null);

    /**
     * @param null $language
     * @return string
     */
    public function getInstructions($language = null);

    /**
     * @param string $instructions
     * @param null $language
     */
    public function setInstructions($instructions, $language = null);

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param int $position
     */
    public function setPosition($position);
}
