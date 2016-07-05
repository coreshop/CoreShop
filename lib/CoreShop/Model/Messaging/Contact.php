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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Messaging;

use CoreShop\Model\AbstractModel;

/**
 * Class Contact
 * @package CoreShop\Model\Messaging
 */
class Contact extends AbstractModel
{
    protected $localizedValues = array('name');

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $description;

    /**
     * Return Threads.
     *
     * @return Thread[]
     */
    public function getThreads()
    {
        $list = Thread::getList();
        $list->setCondition('contactId = ?', array($this->getId()));
        $threads = [];

        foreach ($list->load() as $thread) {
            if (!$thread->getStatus()->getFinished()) {
                $threads[] = $thread;
            }
        }

        return $threads;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param string $language language
     *
     * @return string
     */
    public function getName($language = null)
    {
        return $this->getLocalizedFields()->getLocalizedValue('name', $language);
    }

    /**
     * @param string $name
     * @param string $language language
     */
    public function setName($name, $language = null)
    {
        $this->getLocalizedFields()->setLocalizedValue('name', $name, $language);
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
}
