<?php
class Wsu_Networksecurities_Model_Customer_Observer {
    public function requireLogin($observer) {
        $helper = Mage::helper('wsu_networksecurities');
        $session = Mage::getSingleton('customer/session');
 		$controllerAction = $observer->getEvent()->getControllerAction();
		$helper->filterFrontIp($controllerAction);
		
        if (!$session->isLoggedIn() && $helper->isLoginRequired()) {
           
            /* @var $controllerAction Mage_Core_Controller_Front_Action */
            $requestString = $controllerAction->getRequest()->getRequestString();

            if (!preg_match($helper->getWhitelist(), $requestString)) {
                $session->setBeforeAuthUrl($requestString);
                $controllerAction->getResponse()->setRedirect(Mage::getUrl('customer/account/login'));
                $controllerAction->getResponse()->sendResponse();
                exit;
            }
        }
    }
}