<?php
	if(!defined('__IN_SYMPHONY__')) die('<h2>Error</h2><p>You cannot directly access this file</p>');
	
	//require_once(TOOLKIT . '/class.datasourcemanager.php');	
	//require_once(EXTENSIONS . '/symql/lib/class.symql.php');
	
	Class extension_Checkout_Gateway_SagePay extends Extension{
		
		public function about(){
			return array('name' => 'Sagepay Gateway Extension',
						 'version' => '1.0',
						 'release-date' => '2011-26-06',
						 'author' => array('name' => 'David Anderson',
										   'website' => 'http://veodesign.co.uk',
										   'email' => 'dave@veodesign.co.uk'
						),
				 		'description'	=> 'Adds the Sagepay Gateway to the Symphony Checkout Extension'
						);
		}
		
		public function uninstall() {
			Symphony::Engine()->Configuration->remove('checkout');
			Symphony::Engine()->saveConfig();
		}
		
		public function getSubscribedDelegates() {
			return array(
				array(
					'page'		=> '/system/preferences/',
					'delegate'	=> 'AddCustomPreferenceFieldsets',
					'callback'	=> 'preferences'
				)
			);
		}
		
		public function fetchNavigation() {
			return array(
				array(
					'location'	=> __('System'),
					'name'		=> __('SagePay Options'),
					'link'		=> '/preferences/',
					'limit'		=> 'developer'
				)
			);
		}
		
		//  Get Config Functions ------------------------------------------------------------- 
		
		public function getOrderSectionId(){
			//NEEDS TO RETURN THE ORDER SECTION ID
		}
		
		public function getCustomerOptions(){
			return array( 	'CustomerName','CustomerEmail','BillingSurname','BillingFirstnames','BillingAddress1','BillingAddress2,','BillingAddress3','BillingCity','BillingState','BillingPostcode','BillingCountry','BillingPhone',
							'DeliverySurname','DeliveryFirstnames','DeliveryAddress1','DeliveryAddress2,','DeliveryAddress3','DeliveryCity','DeliveryState','DeliveryPostcode','DeliveryCountry','DeliveryPhone');
		}
		public function getVendorOptions(){
			return array(	'VendorName','EncryptionPassword','CurrencyCode','SuccessURL','FailURL','bSendEmail','OrderDescription','Server')
		}
		
		public function getAllowedOptions(){		
			return array_merge(getCustomerOptions(),getVendorOptions());
		}

		
		public function getConfig($name){
			if(in_array($name,$this->getAllowedOptions())){
				return Symphony::Configuration()->get($name, 'checkout_sagepay_gateway');	
			}
		}
		
		//  Set Config Functions ------------------------------------------------------------- 
		
		public function setConfig($name,$values){
			
			if(in_array($name,$this->getAllowedOptions())){
				if (is_array($values)) {
					$values = implode(',', $values);
					Symphony::Configuration()->set($name, $values, 'checkout_sagepay_gateway');
				}			
				else {
					Symphony::Configuration()->set($name,$values, 'checkout_sagepay_gateway');
				}
				
				Symphony::Engine()->saveConfig();
			}
		}
		
	}
		
