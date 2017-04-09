<?php

namespace CoreShop\Bundle\CurrencyBundle\Twig;

use CoreShop\Bundle\CurrencyBundle\Templating\Helper\FormatMoneyHelperInterface;

final class FormatMoneyExtension extends \Twig_Extension
{
    /**
     * @var FormatMoneyHelperInterface
     */
    private $helper;

    /**
     * @param FormatMoneyHelperInterface $helper
     */
    public function __construct(FormatMoneyHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('coreshop_format_money', [$this->helper, 'formatAmount']),
        ];
    }
}
