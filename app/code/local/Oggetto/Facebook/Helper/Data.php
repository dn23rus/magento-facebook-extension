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
 * Default helper
 *
 * @category   Oggetto
 * @package    Oggetto_Facebook
 * @subpackage Helper
 * @author     Dmitry Buryak <b.dmitry@oggettoweb.com>
 */
class Oggetto_Facebook_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * Url for like button iframe
     *
     * @return string
     */
    public function getUrlForLikeIFrame()
    {
        if ($product = Mage::registry('current_product')) { // check if product page
            $url = $product->getProductUrl();
        } else {
            $url = rtrim(Mage::getBaseUrl(), '/');
        }

        return $url;
    }
}
