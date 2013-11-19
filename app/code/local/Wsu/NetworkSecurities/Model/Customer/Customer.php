<?php
class Wsu_NetworkSecurities_Model_Customer_Customer extends Mage_Customer_Model_Customer {
    /**
     * Authenticate customer
     *
     * @param  string $login
     * @param  string $password
     * @throws Mage_Core_Exception
     * @return true
     *
     */
    public function authenticate($login, $password){
		//$login = trim(mb_convert_kana($login, 'as'));
		$actived = trim(Mage::getStoreConfig('wsu_networksecurities/ldap/customerlogin/activeldap'));
		if (!$actived) //CHECK MAGENTO CONNECT
		        if (!$this->validatePassword($password)) {
					Mage::helper('wsu_networksecurities')->setFailedLogin($login,$password);
				}
				return parent::authenticate($username, $password);

        $this->loadByEmail($login);
        if ($this->getConfirmation() && $this->isConfirmationRequired()) {
            throw Mage::exception('Mage_Core', Mage::helper('customer')->__('This account is not confirmed.'),
                self::EXCEPTION_EMAIL_NOT_CONFIRMED
            );
        }
        if (!$this->validatePassword($password)) {
            Mage::helper('wsu_networksecurities')->setFailedLogin($login,$password);
        }
        Mage::dispatchEvent('customer_customer_authenticated', array(
           'model'    => $this,
           'password' => $password,
        ));

        return true;
    }



}
