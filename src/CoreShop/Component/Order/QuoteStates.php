<?php
declare(strict_types=1);

namespace CoreShop\Component\Order;

final class QuoteStates
{
    public const STATE_INITIALIZED = 'initialized';

    public const STATE_NEW = 'new';

    public const STATE_CANCELLED = 'cancelled';

    public const STATE_COMPLETE = 'complete';
}
