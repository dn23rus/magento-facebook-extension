<?php
/**
 * Oggetto Web extension for Magento
 *
 * Long description of this file (if any...)
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
 * @var $this Mage_Core_Model_Resource_Setup
 */

$installer = $this;

$setup = new Mage_Customer_Model_Resource_Setup('core_setup');

$installer->startSetup();
$installer->getConnection()->beginTransaction();
try {
    $setup->addAttribute('customer', 'facebook_id', array(
        'label'             => 'Facebook Id',
        'required'          => 0,
        'visible'           => 1,
        'sort_order'        => 200,
        'input'             => 'text',
        'adminhtml_only'    => 1.
    ));

    $eavConfig = Mage::getSingleton('eav/config');

    $attr = $eavConfig->getAttribute('customer', 'facebook_id');
    $attr->setData('used_in_forms', array('adminhtml_customer'));
    $attr->save();

} catch (Exception $e) {
    $installer->getConnection()->rollBack();
    Mage::logException($e);
}
$installer->getConnection()->commit();
$installer->endSetup();
