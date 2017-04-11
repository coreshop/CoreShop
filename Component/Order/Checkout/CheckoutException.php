<?php

namespace CoreShop\Component\Order\Checkout;

class CheckoutException extends \RuntimeException
{
    /**
     * @var string
     */
    private $translatableText;

    /**
     * {@inheritdoc}
     */
    public function __construct($reason, $translatableText)
    {
        parent::__construct($reason);

        $this->translatableText = $translatableText;
    }

    /**
     * @return string
     */
    public function getTranslatableText()
    {
        return $this->translatableText;
    }

    /**
     * @param string $translatableText
     */
    public function setTranslatableText($translatableText)
    {
        $this->translatableText = $translatableText;
    }
}
