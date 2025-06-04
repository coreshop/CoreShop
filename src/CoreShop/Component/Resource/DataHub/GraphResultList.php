<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.com/license     GNU General Public License version 3 (GPLv3)
 */

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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Resource\DataHub;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class GraphResultList
{
    public $totalCount;

    public $items;

    public $pageInfo;

    public function __construct(
        array $dataList,
        array $args,
    ) {
        $cnt = 0;

        $maxResults = null;
        $cursor = null;
        $hasMore = false;

        if (isset($args['first'])) {
            $maxResults = $args['first'];
        }

        if ($dataList !== null) {
            foreach ($dataList as $result) {
                if ($maxResults === null || $cnt < $maxResults) {
                    ++$cnt;
                } else {
                    $hasMore = true;
                }
            }
        }

        $this->totalCount = $cnt;
        $this->items = $dataList;
        $this->pageInfo = new GraphPageInfo();
        $this->pageInfo->hasMore = $hasMore;
    }

    public static function getType(string $name, $listType, $pageInfoType)
    {
        $resultFields = [];
        $resultFields[] = [
            'name' => 'totalCount',
            'type' => Type::int(),
        ];
        $resultFields[] = [
            'name' => 'items',
            'type' => Type::listOf($listType),
        ];
        $resultFields[] = [
            'name' => 'pageInfo',
            'type' => $pageInfoType,
        ];

        return new ObjectType(['name' => $name, 'fields' => $resultFields]);
    }
}
