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

require_once Mage::getBaseDir() . '/vendor/facebook/php-sdk/src/facebook.php';

/**
 * Main facebook model.
 *
 * If you haven't facebook/php-sdk then paste in your composer.json require section: "facebook/php-sdk": "v3.2.2".
 * Required version is 3.2.2 or higher.
 *
 * @category   Oggetto
 * @package    Oggetto_Facebook
 * @subpackage Model
 * @author     Dmitry Buryak <b.dmitry@oggettoweb.com>
 */
class Oggetto_Facebook_Model_Facebook extends Mage_Core_Model_Abstract
{
    const XML_PATH_FACEBOOK_ENABLED     = 'facebook/general/enabled';
    const XML_PATH_FACEBOOK_APP_ID      = 'facebook/general/app_id';
    const XML_PATH_FACEBOOK_APP_SECRET  = 'facebook/general/secret';

    const EXCEPTION_CUSTOMER_NOT_EXISTS = 5;

    protected $_facebookModel;

    protected $_userData;

    /**
     * Return facebook model
     *
     * @return Facebook
     * @throws Exception
     */
    public function getFacebookModel()
    {
        if (null === $this->_facebookModel) {
            if (!class_exists('Facebook')) {
                throw new Exception(
                    sprintf('Require include facebook/php-sdk with Facebook class before call %s', __METHOD__)
                );
            }
            $this->_facebookModel = new Facebook(array(
                'appId'  => $this->getFacebookAppId(),
                'secret' => $this->getFacebookSecretKey(),
            ));
        }
        return $this->_facebookModel;
    }

    /**
     * Is facebook functions enabled
     *
     * @return bool
     */
    public function isFacebookEnabled()
    {
        return (Mage::getStoreConfigFlag(self::XML_PATH_FACEBOOK_ENABLED)
            && $this->getFacebookAppId()
            && $this->getFacebookSecretKey());
    }

    /**
     * Facebook app id
     *
     * @return string
     */
    public function getFacebookAppId()
    {
        return Mage::getStoreConfig(self::XML_PATH_FACEBOOK_APP_ID);
    }

    /**
     * Facebook app secret key
     *
     * @return string
     */
    public function getFacebookSecretKey()
    {
        return Mage::getStoreConfig(self::XML_PATH_FACEBOOK_APP_SECRET);
    }

    /**
     * Login url
     *
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->getFacebookModel()->getLoginUrl(array(
            'scope'         => 'email',
            'display'       => 'page',
            'redirect_uri'  => Mage::getUrl('customer/account/facebookLogin'),
        ));
    }

    /**
     * Create account url
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getFacebookModel()->getLoginUrl(array(
            'scope'         => 'email',
            'display'       => 'page',
            'redirect_uri'  => Mage::getUrl('customer/account/facebookCreate'),
        ));
    }

    /**
     * User data
     *
     * @return Varien_Object
     * @throws Mage_Core_Exception
     */
    public function getUserData()
    {
        if (null === $this->_userData) {
            try {
                $this->_userData = new Varien_Object((array) $this->getFacebookModel()->api('/me'));
            } catch (FacebookApiException $e) {
                Mage::logException($e);
                Mage::throwException(Mage::helper('oggetto_fb')->__('Facebook API error'));
            }
        }
        return $this->_userData;
    }

    /**
     * User id
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->getFacebookModel()->getUser();
    }

    /**
     * Login with facebook.
     *
     * @param Mage_Customer_Model_Session $session session
     * @return Oggetto_Facebook_Model_Facebook
     * @throws Mage_Core_Exception
     */
    public function loginCustomer($session = null)
    {
        /* @var $session Mage_Customer_Model_Session */
        $session = $session ? $session : Mage::getSingleton('customer/session');
        if (!$session->isLoggedIn() && $this->getUserId()) {
            $customer = Mage::getModel('customer/customer');
            $customer
                ->setWebsiteId(Mage::app()->getWebsite()->getId())
                ->loadByEmail($this->getUserData()->getEmail());
            if ($customer->getEntityId()) {
                $session->loginById($customer->getEntityId());
            } else {
                throw new Mage_Core_Exception(
                    Mage::helper('oggetto_fb')->__('Customer not exits.'),
                    self::EXCEPTION_CUSTOMER_NOT_EXISTS
                );
            }
        }

        return $this;
    }

    /**
     * Create customer with facebook data
     *
     * @param Mage_Core_Controller_Request_Http $request request instance
     * @return Oggetto_Facebook_Model_Facebook
     * @throws Mage_Core_Exception
     */
    public function createCustomer($request)
    {
        $data     = $this->getUserData();
        $customer = Mage::getModel('customer/customer');

        $customer->setEmail($data->getEmail())
            ->setFirstname($data->getFirstName())
            ->setLastname($data->getLastName())
            ->setAccountConfirmation(1)
            ->setPassword($customer->generatePassword())
            ->setConfirmation($customer->getPassword())
            ->setFacebookId($data->getId());

        $errors = $this->_validateCustomer($request, $customer);
        if ($errors !== true) {
            Mage::throwException(nl2br(implode(PHP_EOL, $errors)));
        }

        $customer->save();
        $customer->sendPasswordReminderEmail();

        return $this;
    }

    /**
     * Validate customer
     *
     * @param Mage_Core_Controller_Request_Http $request  request instance
     * @param Mage_Customer_Model_Customer      $customer customer instance
     * @return bool|array
     */
    protected function _validateCustomer($request, $customer)
    {
        $customerForm = $this->_getCustomerForm($customer);
        $customerData = $customerForm->extractData($request);
        $errors       = $customerForm->validateData($customerData);
        if ($errors === true) {
            $customerForm->compactData($customerData);
            $errors = $customer->validate();
        }
        return $errors;
    }

    /**
     * Customer form initialized model
     *
     * @param Mage_Customer_Model_Customer $customer customer instance
     * @return Mage_Customer_Model_Form
     */
    protected function _getCustomerForm($customer)
    {
        $customerForm = Mage::getModel('customer/form');
        $customerForm->setFormCode('customer_account_create');
        $customerForm->setEntity($customer);
        return $customerForm;
    }
}
