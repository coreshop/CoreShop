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

namespace CoreShop\Bundle\FixtureBundle\Event;

final class FixturesEvents
{
    /**
     * This event is raised before data fixtures are loaded.
     *
     * @var string
     */
    public const DATA_FIXTURES_PRE_LOAD = 'coreshop.data_fixtures.pre_load';

    /**
     * This event is raised after data fixtures are loaded.
     *
     * @var string
     */
    public const DATA_FIXTURES_POST_LOAD = 'coreshop.data_fixtures.post_load';
}
