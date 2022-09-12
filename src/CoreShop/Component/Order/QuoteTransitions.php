<?php
declare(strict_types=1);

namespace CoreShop\Component\Order;

final class QuoteTransitions
{
    public const IDENTIFIER = 'coreshop_quote';

    public const TRANSITION_CREATE = 'create';

    public const TRANSITION_CANCEL = 'cancel';

    public const TRANSITION_COMPLETE = 'complete';
}
