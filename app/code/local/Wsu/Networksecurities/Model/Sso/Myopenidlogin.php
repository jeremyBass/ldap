<?php
class Wsu_Networksecurities_Model_Sso_Myopenidlogin extends Wsu_Networksecurities_Model_Sso_Abstract {
	public function createProvider() {
		try{
			require_once Mage::getBaseDir('base').DS.'lib'.DS.'OpenId'.DS.'openid.php';
		}catch(Exception $e) {}
		$openid = new LightOpenID(Mage::getUrl());       
		return $openid;
	}
	public function getLoginUrl($identity="") {
		$my_id = $this->getProvider();
        $my = $this->setIdlogin($my_id,$identity);
		$loginUrl = $my->authUrl();
		return $loginUrl;
	}
	public function setIdlogin($openid,$identity) {
        $openid->identity = "http://".$identity.".myopenid.com";
        $openid->required = array(
			'namePerson/first',
			'namePerson/last',
			'namePerson/friendly',
			'contact/email',
			'namePerson' 
        );
        $openid->returnUrl = Mage::getUrl('sociallogin/myopenidlogin/login');
		return $openid;
    }
}
  
