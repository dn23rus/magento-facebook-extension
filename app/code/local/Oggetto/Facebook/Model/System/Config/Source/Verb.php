<?php
/**
 * Oggetto Web extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Oggetto Facebook module to newer versions in the future.
 * If you wish to customize the Oggetto Facebook module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Oggetto
 * @package    Oggetto_Facebook
 * @copyright  Copyright (C) 2013 Oggetto Web (http://oggettoweb.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * System config verb to display options
 *
 * @category   Oggetto
 * @package    Oggetto_Facebook
 * @subpackage Model
 * @author     Dmitry Buryak <b.dmitry@oggettoweb.com>
 */
class Oggetto_Facebook_Model_System_Config_Source_Verb
{
    const DEFAULT_VALUE = 'like';

    /**
     * Options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::DEFAULT_VALUE,
                'label' => Mage::helper('oggetto_fb')->__('Like'),
            ),
            array(
                'value' => 'recommend',
                'label' => Mage::helper('oggetto_fb')->__('Recommend'),
            ),
        );
    }
}
