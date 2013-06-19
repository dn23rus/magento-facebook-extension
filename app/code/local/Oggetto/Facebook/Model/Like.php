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
 * Like button model
 *
 * @category   Oggetto
 * @package    Oggetto_Facebook
 * @subpackage Model
 * @author     Dmitry Buryak <b.dmitry@oggettoweb.com>
 */
class Oggetto_Facebook_Model_Like extends Mage_Core_Model_Abstract
{
    const XML_PATH_LIKE_ENABLED = 'facebook/like_button/enabled';

    protected $_layoutPrefix = 'fb_like_button_';

    /**
     * @var Varien_Object
     */
    protected $_options;

    /**
     *  Check if button with given name in layout is enabled
     *
     * @param string $string string
     * @return bool
     */
    public function isEnabled($string)
    {
        return Mage::getSingleton('oggetto_fb/facebook')->isFacebookEnabled()
            && Mage::getStoreConfigFlag($this->getConfigPath($string));
    }

    /**
     * Config path
     *
     * @param string $string name in layout
     * @return string
     */
    public function getConfigPath($string)
    {
        $configPath = str_replace($this->_layoutPrefix, '', $string);
        return 'facebook/like_button/' . $configPath;
    }

    /**
     * Like button options
     *
     * @return Varien_Object
     */
    public function getButtonOptions()
    {
        if (null === $this->_options) {
            $this->_options = new Varien_Object(array(
                'appId'         => Mage::getSingleton('oggetto_fb/facebook')->getFacebookAppId(),
                'layout'        => Mage::getStoreConfig('facebook/like_button/layout_style'),
                'width'         => Mage::getStoreConfig('facebook/like_button/width'),
                'font'          => Mage::getStoreConfig('facebook/like_button/font'),
                'show_faces'    => var_export(Mage::getStoreConfigFlag('facebook/like_button/show_faces'), true),
            ));
            $verb  = Mage::getStoreConfig('facebook/like_button/verb');
            $color = Mage::getStoreConfig('facebook/like_button/color_scheme');
            if ($verb != Oggetto_Facebook_Model_System_Config_Source_Verb::DEFAULT_VALUE) {
                $this->_options->setData('action', $verb);
            }
            if ($color != Oggetto_Facebook_Model_System_Config_Source_Color::DEFAULT_VALUE) {
                $this->_options->setData('colorscheme', $color);
            }
        }
        return $this->_options;
    }
}
