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
 * Facebook javascript sdk
 *
 * @category   Oggetto
 * @package    Oggetto_Facebook
 * @subpackage Block
 * @author     Dmitry Buryak <b.dmitry@oggettoweb.com>
 */
class Oggetto_Facebook_Block_JsSdk extends Mage_Core_Block_Template
{
    /**
     * Return JavaScript Sdk html
     *
     * @return string
     */
    public function getJsSdkHtml()
    {
        $lang  = Mage::app()->getLocale()->getLocaleCode();
        $appId = Mage::getSingleton('oggetto_fb/facebook')->getFacebookModel()->getAppId();
        return <<<"JSSDK"
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/{$lang}/all.js#xfbml=1&appId={$appId}";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
JSSDK;
    }

    /**
     * To html
     *
     * @return string
     */
    protected function _toHtml()
    {
        return Mage::getSingleton('oggetto_fb/facebook')->isFacebookEnabled() ? $this->getJsSdkHtml() . PHP_EOL : '';
    }
}
