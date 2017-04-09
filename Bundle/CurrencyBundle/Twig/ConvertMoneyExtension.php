<?php

namespace CoreShop\Bundle\CurrencyBundle\Twig;

use CoreShop\Bundle\CurrencyBundle\Templating\Helper\ConvertMoneyHelperInterface;

final class ConvertMoneyExtension extends \Twig_Extension
{
    /**
     * @var ConvertMoneyHelperInterface
     */
    private $helper;

    /**
     * @param ConvertMoneyHelperInterface $helper
     */
    public function __construct(ConvertMoneyHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('coreshop_convert_money', [$this->helper, 'convertAmount']),
        ];
    }
}
