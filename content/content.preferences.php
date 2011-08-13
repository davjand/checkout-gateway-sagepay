<?php

	require_once(TOOLKIT . '/class.administrationpage.php');
	
	require_once(TOOLKIT . '/class.datasourcemanager.php');		
	require_once(TOOLKIT . '/class.sectionmanager.php');
	require_once(TOOLKIT . '/class.fieldmanager.php');
	
	class contentExtensionCheckoutPreferences extends AdministrationPage {
		protected $driver;
		
		public function __viewIndex() {
			$this->driver = Symphony::ExtensionManager()->create('checkout_gateway_sagepay');
			$bIsWritable = true;
			
		    if (!is_writable(CONFIG)) {
		        $this->pageAlert(__('The Symphony configuration file, <code>/manifest/config.php</code>, is not writable. You will not be able to save changes to preferences.'), AdministrationPage::PAGE_ALERT_ERROR);
		        $bIsWritable = false;
		    }
			
			$this->setPageType('form');
			$this->setTitle('Symphony &ndash; ' . __('SagePay Gateway Options'));
			
			$this->appendSubheading(__('SagePay Gateway Options'));

			
		// Vendor Settings --------------------------------------------------------
			
			$orderSection=$this->driver->getOrderSectionId()		

			
			$container = new XMLElement('fieldset');
			$container->setAttribute('class', 'settings');
			$container->appendChild(
				new XMLElement('legend', __('Vendor Settings'))
			);
			$p=new XMLElement('p',__('Please enter your SagePay Vendor Information'));			
			$container->appendChild($p);
			
			
			$vendorFields = $this->driver->getVendorOptions();
			
			//loop through the array, building the form
			$group = new XMLElement('div');
			$group->setAttribute('class', 'group');
			
			for($i = 0; $i< sizeof($vendorFields); $i++){
				
				$label = Widget::Label(__($vendorFields[$i]));
				$label->appendChild(Widget::Input($vendorFields[$i], $this->driver->getConfig($vendorFields[$i]), 'input'));	
				$group->appendChild($label);	
			
				if($i % 2 == 0){
					$container->appendChild($group);
					$group = new XMLElement('div');
					$group->setAttribute('class', 'group');
				}			
			}
			
			$this->Form->appendChild($container);
			
			
		
		// Customer Settings --------------------------------------------------------
			
			//setup the XML Element and add a title
			$container = new XMLElement('fieldset');
			$container->setAttribute('class', 'settings');
			$container->appendChild(
				new XMLElement('legend', __('Customer Settings'))
			);

			$p=new XMLElement('p',__('Please select the fields used for each property. Please consult the readme for more information'));			
			$container->appendChild($p);
			
			
			//get an array of the customer fields
			$customerFields = $this->driver->getCustomerFields();
			
			//loop through the array, building the form
			$group = new XMLElement('div');
			$group->setAttribute('class', 'group');
			
			for($i = 0; $i< sizeof($customerFields); $i++){
				
				$label = Widget::Label(__($customerFields[$i]));
				$this->__appendFieldSelect($customerFields[$i],$label,$orderSection);			
				$group->appendChild($label);	
			
				if($i % 2 == 0){
					$container->appendChild($group);
					$group = new XMLElement('div');
					$group->setAttribute('class', 'group');
				}			
			}
			
			$this->Form->appendChild($container);
			
			/*Submit Button*/
			$div = new XMLElement('div');
			$div->setAttribute('class', 'actions');
			
			$attr = array('accesskey' => 's');
			if (!$bIsWritable) $attr['disabled'] = 'disabled';
			$div->appendChild(Widget::Input('action[save]', __('Save Changes'), 'submit', $attr));

			$this->Form->appendChild($div);
		}

		
	
		
	/*-------------------------------------------------------------------------
		Field List Generator:
	-------------------------------------------------------------------------*/
		public function __appendFieldSelect($option,$context,$section=NULL){			
			
			if($section==NULL){
				$select= Widget::Select($option.'[]', array(), array('disabled'=>'disabled'));
			}
			else{
				
				$data = $this->driver->getConfig($option);
				//$checkedData = $data != false ? $data : '-1';
				
				$fieldManager = new FieldManager($this);
				$fields = $fieldManager->fetch(null,$section);
				
				if($fields)
				{
					$options = array(array('','','--Please Select--'));
					foreach($fields as $field)
					{			
						$options[] = array($field->get('id'), $field->get('id') == $data, $field->get('label'));
					}
					$select= Widget::Select($option.'[]', $options);		
				}
				else{
					$select= Widget::Select($option.'[]', array(), array('disabled'=>'disabled'));	
				}
			}
			$context->appendChild($select);
			
		}

	/*-------------------------------------------------------------------------
		Save Function
	-------------------------------------------------------------------------*/	
		
		public function __actionIndex() {
			
			$settings  = @$_POST;
			
			if (empty($this->driver)) {
				$this->driver = Symphony::ExtensionManager()->create('checkout_gateway_sagepay');
			}
			
			if (@isset($_POST['action']['save'])) {
				
				foreach($settings as $key => $value){
					$this->driver->setConfig($key,$value);
				}
			}
		}
	}
	
?>
