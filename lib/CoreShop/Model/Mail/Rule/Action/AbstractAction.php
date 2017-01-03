<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Mail\Rule\Action;

use CoreShop\Model\Cart;
use CoreShop\Model;
use Pimcore\Model\AbstractModel;

/**
 * Class AbstractAction
 * @package CoreShop\Model\Mail\Rule\Action
 */
abstract class AbstractAction extends Model\Rules\Action\AbstractAction
{
    /**
     * apply action
     *
     * @param AbstractModel $model
     * @param Model\Mail\Rule $rule
     * @param array $params
     */
    public function apply(AbstractModel $model, Model\Mail\Rule $rule, $params = [])
    {

    }
}
