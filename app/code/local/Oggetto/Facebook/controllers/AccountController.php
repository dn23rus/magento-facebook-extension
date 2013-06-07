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

require_once Mage::getModuleDir('controllers', 'Mage_Customer') . DS . 'AccountController.php';

/**
 * AccountController.php
 *
 * @category   Oggetto
 * @package    Oggetto_Facebook
 * @subpackage controller
 * @author     Dmitry Buryak <b.dmitry@oggettoweb.com>
 */
class Oggetto_Facebook_AccountController extends Mage_Customer_AccountController
{
    /**
     * pre-dispatch
     *
     * @return void
     */
    public function preDispatch()
    {
        Mage_Core_Controller_Front_Action::preDispatch();

        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        $action = $this->getRequest()->getActionName();
        $openActions = array(
            'create',
            'login',
            'logoutsuccess',
            'forgotpassword',
            'forgotpasswordpost',
            'resetpassword',
            'resetpasswordpost',
            'confirm',
            'confirmation',
            'facebookLogin'
        );
        $pattern = '/^(' . implode('|', $openActions) . ')/i';

        if (!preg_match($pattern, $action)) {
            if (!$this->_getSession()->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        } else {
            $this->_getSession()->setNoReferer(true);
        }
    }

    /**
     * Facebook login action
     *
     * @return void
     */
    public function facebookLoginAction()
    {
        try {
            /* @var $facebookModel Oggetto_Facebook_Model_Facebook */
            $facebookModel = Mage::getSingleton('oggetto_fb/facebook');
            $facebookModel->loginCustomer($this->getRequest(), $this->_getSession());
            $this->_redirect('customer/account/index');
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()
                ->addError(Mage::helper('oggetto_fb')->__('Something went wrong while login with Facebook'));
        }
        $this->_redirect('customer/account/login');
    }
}

