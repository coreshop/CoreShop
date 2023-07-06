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
        array $args
    ) {
        $cnt = 0;

        $maxResults = null;
        $cursor = null;
        $hasMore = false;

        if (isset($args['first'])) {
            $maxResults = $args['first'];
        }

        if ($dataList !== null)
        {
            foreach ($dataList as $result)
            {
                if ($maxResults === null || $cnt < $maxResults) {
                    $cnt++;
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
        $resultFields = array();
        $resultFields[] = array(
            'name' => 'totalCount',
            'type' => Type::int(),
        );
        $resultFields[] = array(
            'name' => 'items',
            'type' => Type::listOf($listType),
        );
        $resultFields[] = array(
            'name' => 'pageInfo',
            'type' => $pageInfoType,
        );

        return new ObjectType(array('name' => $name, 'fields' => $resultFields));
    }
}
