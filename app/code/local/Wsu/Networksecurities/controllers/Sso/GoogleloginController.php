<?php
class Wsu_Networksecurities_Sso_GoogleloginController extends Mage_Core_Controller_Front_Action{
	
	public function loginAction() {		
		
		if (!$this->getAuthorizedToken()) {
			$token = $this->getAuthorization();
		}else{ $token = $this->getAuthorizedToken();
		}
		
        return $token;
    }
	
	public function userAction() {
		$customerHelper = Mage::helper('wsu_networksecurities/customer');
		$googlelogin = Mage::getModel('wsu_networksecurities/sso_googlelogin');
		$oauth2 = new Google_Oauth2Service($googlelogin);
		$code = $this->getRequest()->getParam('code');
		if(!$code) {
			Mage::getSingleton('core/session')->addError('Login failed as you have not granted access.');
			$customerHelper->setJsRedirect(Mage::getBaseUrl());	
		}
		$accessToken = $googlelogin->authenticate($code);						
		$client = $oauth2->userinfo->get();
		
		$user = array();		
		$email = $client['email'];		
		$name = $client['name'];
		$arrName = explode(' ', $name, 2);
		$user['firstname'] = $arrName[0];
		$user['lastname'] = $arrName[1];			
		$user['email'] = $email;
		
		//get website_id and sote_id of each stores
		$store_id = Mage::app()->getStore()->getStoreId();//add
		$website_id = Mage::app()->getStore()->getWebsiteId();//add
		
		$customer = $customerHelper->getCustomerByEmail($user['email'],$website_id );//add edition
		if(!$customer || !$customer->getId()) {
			//Login multisite
			$customer = $customerHelper->createCustomerMultiWebsite($user, $website_id, $store_id );
			if (Mage::getStoreConfig('wsu_networksecurities/googlelogin/is_send_password_to_customer')) {
				$customer->sendPasswordReminderEmail();
			}
		}
		// fix confirmation
		if ($customer->getConfirmation()) {
			try {
				$customer->setConfirmation(null);
				$customer->save();
			}catch (Exception $e) {
			}
		}
		Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
		$customerHelper->setJsRedirect($customerHelper->_loginPostRedirect());	
    }
	
	// if exit access token
	public function getAuthorizedToken() {
        $token = false;
        if (!is_null(Mage::getSingleton('core/session')->getAccessToken())) {
            $token = unserialize(Mage::getSingleton('core/session')->getAccessToken());
        }
        return $token;
    }
     
	// if not exit access token
     public function getAuthorization() {      
       	$scope = array(
					'https://www.googleapis.com/auth/userinfo.profile',
					'https://www.googleapis.com/auth/userinfo.email'
				 );		
		$googlelogin = Mage::getModel('wsu_networksecurities/sso_googlelogin');        			
		$googlelogin->setScopes($scope); 		
		$googlelogin->authenticate();					
		$authUrl = $googlelogin->createAuthUrl();
		header('Localtion: '.$authUrl);
		die(0);
    }
}