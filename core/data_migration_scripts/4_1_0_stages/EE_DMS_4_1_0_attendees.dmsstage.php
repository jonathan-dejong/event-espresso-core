<?php 
/**
 * migrates 3.1 attendee rows into 4.1 registrations, attendees, transactions, line items, payments
 * 
 * 3.1 Attendee table definition:
 * delimiter $$

CREATE TABLE `wp_events_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `event_code` varchar(26) DEFAULT '0',
  `event_name` varchar(100) DEFAULT NULL,
  `event_desc` text,
  `display_desc` varchar(1) DEFAULT 'Y',
  `display_reg_form` varchar(1) DEFAULT 'Y',
  `event_identifier` varchar(75) DEFAULT NULL,
  `start_date` varchar(15) DEFAULT NULL,
  `end_date` varchar(15) DEFAULT NULL,
  `registration_start` varchar(15) DEFAULT NULL,
  `registration_end` varchar(15) DEFAULT NULL,
  `registration_startT` varchar(15) DEFAULT NULL,
  `registration_endT` varchar(15) DEFAULT NULL,
  `visible_on` varchar(15) DEFAULT NULL,
  `address` text,
  `address2` text,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `zip` varchar(11) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `venue_title` varchar(250) DEFAULT NULL,
  `venue_url` varchar(250) DEFAULT NULL,
  `venue_image` text,
  `venue_phone` varchar(15) DEFAULT NULL,
  `virtual_url` varchar(250) DEFAULT NULL,
  `virtual_phone` varchar(15) DEFAULT NULL,
  `reg_limit` varchar(25) DEFAULT '999999',
  `allow_multiple` varchar(15) DEFAULT 'N',
  `additional_limit` int(10) DEFAULT '5',
  `send_mail` varchar(2) DEFAULT 'Y',
  `is_active` varchar(1) DEFAULT 'Y',
  `event_status` varchar(2) DEFAULT 'A',
  `conf_mail` text,
  `use_coupon_code` varchar(1) DEFAULT 'N',
  `use_groupon_code` varchar(1) DEFAULT 'N',
  `category_id` text,
  `coupon_id` text,
  `tax_percentage` float DEFAULT NULL,
  `tax_mode` int(11) DEFAULT NULL,
  `member_only` varchar(1) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL,
  `post_type` varchar(50) DEFAULT NULL,
  `country` varchar(200) DEFAULT NULL,
  `externalURL` varchar(255) DEFAULT NULL,
  `early_disc` varchar(10) DEFAULT NULL,
  `early_disc_date` varchar(15) DEFAULT NULL,
  `early_disc_percentage` varchar(1) DEFAULT 'N',
  `question_groups` longtext,
  `item_groups` longtext,
  `event_type` varchar(250) DEFAULT NULL,
  `allow_overflow` varchar(1) DEFAULT 'N',
  `overflow_event_id` int(10) DEFAULT '0',
  `recurrence_id` int(11) DEFAULT '0',
  `email_id` int(11) DEFAULT '0',
  `alt_email` text,
  `event_meta` longtext,
  `wp_user` int(22) DEFAULT '1',
  `require_pre_approval` int(11) DEFAULT '0',
  `timezone_string` varchar(250) DEFAULT NULL,
  `likes` int(22) DEFAULT NULL,
  `ticket_id` int(22) DEFAULT '0',
  `submitted` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_code` (`event_code`),
  KEY `wp_user` (`wp_user`),
  KEY `event_name` (`event_name`),
  KEY `city` (`city`),
  KEY `state` (`state`),
  KEY `start_date` (`start_date`),
  KEY `end_date` (`end_date`),
  KEY `registration_start` (`registration_start`),
  KEY `registration_end` (`registration_end`),
  KEY `reg_limit` (`reg_limit`),
  KEY `event_status` (`event_status`),
  KEY `recurrence_id` (`recurrence_id`),
  KEY `submitted` (`submitted`),
  KEY `likes` (`likes`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8$$



 * 4.1 Attendee tables and fields:
 * $this->_tables = array(
			'Attendee_CPT'=> new EE_Primary_Table('posts', 'ID'),
			'Attendee_Meta'=>new EE_Secondary_Table('esp_attendee_meta', 'ATTM_ID', 'ATT_ID')
		);
		$this->_fields = array(
			'Attendee_CPT'=>array(
				'ATT_ID'=>new EE_Primary_Key_Int_Field('ID', __("Attendee ID", "event_espresso")),
				'ATT_full_name'=>new EE_Plain_Text_Field('post_title', __("Attendee Full Name", "event_espresso"), false, __("Unknown", "event_espresso")),
				'ATT_bio'=>new EE_Simple_HTML_Field('post_content', __("Attendee Biography", "event_espresso"), false, __("No Biography Provided", "event_espresso")),
				'ATT_slug'=>new EE_Slug_Field('post_name', __("Attendee URL Slug", "event_espresso"), false),
				'ATT_created'=>new EE_Datetime_Field('post_date', __("Time Attendee Created", "event_espresso"), false, current_time('timestamp')),
				'ATT_short_bio'=>new EE_Simple_HTML_Field('post_excerpt', __("Attendee Short Biography", "event_espresso"), true, __("No Biography Provided", "event_espresso")),
				'ATT_modified'=>new EE_Datetime_Field('post_modified', __("Time Attendee Last Modified", "event_espresso"), true, current_time('timestamp')),
				'ATT_author'=>new EE_Integer_Field('post_author', __("WP User that Created Attendee", "event_espresso"), false,0),
				'ATT_parent'=>new EE_DB_Only_Int_Field('post_parent', __("Parent Attendee (unused)", "event_espresso"), true),
				'post_type'=>new EE_DB_Only_Text_Field('post_type', __("Post Type of Attendee", "event_espresso"), false,'espresso_attendees'),
				'status' => new EE_WP_Post_Status_Field('post_status', __('Attendee Status', 'event_espresso'), false, 'publish')
			),
			'Attendee_Meta'=>array(
				'ATTM_ID'=> new EE_DB_Only_Int_Field('ATTM_ID', __('Attendee Meta Row ID','event_espresso'), false),
				'ATT_ID_fk'=>new EE_DB_Only_Int_Field('ATT_ID', __("Foreign Key to Attendee in Post Table", "event_espresso"), false),
				'ATT_fname'=>new EE_Plain_Text_Field('ATT_fname', __('First Name','event_espresso'), true, ''),
				'ATT_lname'=>new EE_Plain_Text_Field('ATT_lname', __('Last Name','event_espresso'), true, ''),
				'ATT_address'=>new EE_Plain_Text_Field('ATT_address', __('Address Part 1','event_espresso'), true, ''),
				'ATT_address2'=>new EE_Plain_Text_Field('ATT_address2', __('Address Part 2','event_espresso'), true, ''),
				'ATT_city'=>new EE_Plain_Text_Field('ATT_city', __('City','event_espresso'), true, ''),
				'STA_ID'=>new EE_Foreign_Key_Int_Field('STA_ID', __('State','event_espresso'), true,0,'State'),
				'CNT_ISO'=>new EE_Foreign_Key_String_Field('CNT_ISO', __('Country','event_espresso'), true,'','Country'),
				'ATT_zip'=>new EE_Plain_Text_Field('ATT_zip', __('ZIP/Postal Code','event_espresso'), true, ''),
				'ATT_email'=>new EE_Email_Field('ATT_email', __('Email Address','event_espresso'), true, ''),
				'ATT_phone'=>new EE_Plain_Text_Field('ATT_phone', __('Phone','event_espresso'), true, ''),
				'ATT_social'=>new EE_Serialized_Text_Field('ATT_social', __("Social Information", "event_espresso"), true, null),
				'ATT_comments'=>new EE_Simple_HTML_Field('ATT_comments', __("Comments by Attendee", "event_espresso"), false,''),
				'ATT_notes'=>new EE_Simple_HTML_Field('ATT_notes', __('Admin Notes','event_espresso'), true, ''),
			));
 * 
 * 4.1 Registration tables and models:
 * $this->_tables = array(
			'Registration'=>new EE_Primary_Table('esp_registration','REG_ID')
		);
		$this->_fields = array(
			'Registration'=>array(
				'REG_ID'=>new EE_Primary_Key_Int_Field('REG_ID', __('Registration ID','event_espresso')),
				'EVT_ID'=>new EE_Foreign_Key_Int_Field('EVT_ID', __('Even tID','event_espresso'), false, 0, 'Event'),
				'ATT_ID'=>new EE_Foreign_Key_Int_Field('ATT_ID', __('Attendee ID','event_espresso'), false, 0, 'Attendee'),
				'TXN_ID'=>new EE_Foreign_Key_Int_Field('TXN_ID', __('Transaction ID','event_espresso'), false, 0, 'Transaction'),
				'TKT_ID'=>new EE_Foreign_Key_Int_Field('TKT_ID', __('Ticket ID','event_espresso'), false, 0, 'Ticket'),
				'STS_ID'=>new EE_Foreign_Key_String_Field('STS_ID', __('Status ID','event_espresso'), false, EEM_Registration::status_id_not_approved, 'Status'),
				'REG_date'=>new EE_Datetime_Field('REG_date', __('Time registration occured','event_espresso'), false, current_time('timestamp'), $timezone ),
				'REG_final_price'=>new EE_Money_Field('REG_final_price', __('Final Price of registration','event_espresso'), false, 0),
				'REG_session'=>new EE_Plain_Text_Field('REG_session', __('Session ID of registration','event_espresso'), false, ''),
				'REG_code'=>new EE_Plain_Text_Field('REG_code', __('Unique Code for this registration','event_espresso'), false, ''),
				'REG_url_link'=>new EE_Plain_Text_Field('REG_url_link', __('String to be used in URL for identifying registration','event_espresso'), false, ''),
				'REG_count'=>new EE_Integer_Field('REG_count', __('Count of this registration in the group registraion ','event_espresso'), true, 1),
				'REG_group_size'=>new EE_Integer_Field('REG_group_size', __('Number of registrations on this group','event_espresso'), false, 1),
				'REG_att_is_going'=>new EE_Boolean_Field('REG_att_is_going', __('Flag indicating the registrant plans on attending','event_espresso'), false, false),
				'REG_deleted' => new EE_Trashed_Flag_Field('REG_deleted', __('Flag indicating if registration has been archived or not.', 'event_espresso'), false, false )	
			)
		);
 * 
 * 4.1 Transaction tables and models:
 * $this->_tables = array(
			'Transaction'=>new EE_Primary_Table('esp_transaction','TXN_ID')
		);
		$this->_fields = array(
			'Transaction'=>array(
				'TXN_ID'=>new EE_Primary_Key_Int_Field('TXN_ID', __('Transaction ID','event_espresso')),
				'TXN_timestamp'=>new EE_Datetime_Field('TXN_timestamp', __('date when transaction was created','event_espresso'), false, current_time('timestamp'), $timezone ),
				'TXN_total'=>new EE_Money_Field('TXN_total', __('Total value of Transaction','event_espresso'), false, 0),
				'TXN_paid'=>new EE_Money_Field('TXN_paid', __('Amount paid towards transaction to date','event_espresso'), false, 0),
				'STS_ID'=>new EE_Foreign_Key_String_Field('STS_ID', __('Status ID','event_espresso'), false, EEM_Transaction::incomplete_status_code, 'Status'),
				'TXN_tax_data'=>new EE_Serialized_Text_Field('TXN_tax_data', __('Serialized mess of tax data','event_espresso'), true, ''),
				'TXN_session_data'=>new EE_Serialized_Text_Field('TXN_session_data', __('Serialized mess of session data','event_espresso'), true, ''),
				'TXN_hash_salt'=>new EE_Plain_Text_Field('TXN_hash_salt', __('Transaction Hash Salt','event_espresso'), true, '')
			)
		);
 * 
 * 4.1 Payment tables and models:
 * $this->_tables = array(
			'Payment'=>new EE_Primary_Table('esp_payment','PAY_ID')
		);
		$this->_fields = array(
			'Payment'=>array(
				'PAY_ID'=>new EE_Primary_Key_Int_Field('PAY_ID', __('Payment ID','event_espresso')),
				'TXN_ID'=>new EE_Foreign_Key_Int_Field('TXN_ID', __('Transaction ID','event_espresso'), false, 0, 'Transaction'),
				'STS_ID'=>new EE_Foreign_Key_String_Field('STS_ID', __('STatus ID','event_espresso'), false, EEM_Payment::status_id_cancelled, 'Status'),
				'PAY_timestamp'=> new EE_Datetime_Field('PAY_timestamp', __('Timestamp of when payment was attemped','event_espresso'), false, current_time('timestamp'), $timezone ),
				'PAY_method'=>new EE_All_Caps_Text_Field('PAY_method', __('User-friendly description of payment','event_espresso'), false, 'CART'),
				'PAY_amount'=>new EE_Money_Field('PAY_amount', __('Amount Payment should be for','event_espresso'), false, 0),
				'PAY_gateway'=>new EE_Plain_Text_Field('PAY_gateway', __('Gateway name used for payment','event_espresso'), false, __('Unspecified','event_espresso')),
				'PAY_gateway_response'=>new EE_Plain_Text_Field('PAY_gateway_response', __('Response from Gateway about the payment','event_espresso'), false, ''),
				'PAY_txn_id_chq_nmbr'=>new EE_Plain_Text_Field('PAY_txn_id_chq_nmbr', __('Transaction ID or Cheque Number','event_espresso'), true, ''),
				'PAY_po_number'=>new EE_Plain_Text_Field('PAY_po_number', __('Purchase or Sales Number','event_espresso'), true, ''),
				'PAY_extra_accntng'=>new EE_Simple_HTML_Field('PAY_extra_accntng', __('Extra Account Info','event_espresso'), true, ''),
				'PAY_via_admin'=>new EE_Boolean_Field('PAY_via_admin', __('Whehter payment made via admin','event_espresso'), false, false),
				'PAY_details'=>new EE_Serialized_Text_Field('PAY_details', __('Full Gateway response about payment','event_espresso'), true, '')
			)
		);
4.1 Line Item table fields
 * $this->_tables = array(
			'Line_Item'=>new EE_Primary_Table('esp_line_item','LIN_ID')
		);
		$line_items_can_be_for = array('Ticket','Price');
		$this->_fields = array(
			'Line_Item'=> array(
				'LIN_ID'=>new EE_Primary_Key_Int_Field('LIN_ID', __("ID", "event_espresso")),
				'LIN_code'=>new EE_Slug_Field('LIN_code', __("Code for index into Cart", "event_espresso"), true),
				'TXN_ID'=>new EE_Foreign_Key_Int_Field('TXN_ID', __("Transaction ID", "event_espresso"), true, null, 'Transaction'),
				'LIN_name'=>new EE_Full_HTML_Field('LIN_name', __("Line Item Name", "event_espresso"), false, ''),
				'LIN_desc'=>new EE_Full_HTML_Field('LIN_desc', __("Line Item Description", "event_espresso"), true),
				'LIN_unit_price'=>new EE_Money_Field('LIN_unit_price',  __("Unit Price", "event_espresso"),false,0),
				'LIN_percent'=>new EE_Float_Field('LIN_percent', __("Percent", "event_espresso"), false, false),
				'LIN_is_taxable'=>new EE_Boolean_Field('LIN_is_taxable', __("Taxable", "event_espresso"), false, false),
				'LIN_order'=>new EE_Integer_Field('LIN_order', __("Order of Application towards total of parent", "event_espresso"), false,1),
				'LIN_total'=>new EE_Money_Field('LIN_total', __("Total (unit price x quantity)", "event_espresso"), false, 0),
				'LIN_quantity'=>new EE_Integer_Field('LIN_quantity', __("Quantity", "event_espresso"), true, null),
				'LIN_parent'=>new EE_Integer_Field('LIN_parent', __("Parent ID (this item goes towards that Line Item's total)", "event_espresso"), true, null),
				'LIN_type'=>new EE_Enum_Text_Field('LIN_type', __("Type", "event_espresso"), false, 'line-item', 
						array(
							self::type_line_item=>  __("Line Item", "event_espresso"),
							self::type_sub_line_item=>  __("Sub-Item", "event_espresso"),
							self::type_sub_total=>  __("Subtotal", "event_espresso"),
							self::type_tax_sub_total => __("Tax Subtotal", "event_espresso"),
							self::type_tax=>  __("Tax", "event_espresso"),
							self::type_total=>  __("Total", "event_espresso"))),
				'OBJ_ID'=>new EE_Foreign_Key_Int_Field('OBJ_ID', __("ID of Item purchased.", "event_espresso"), true,null,$line_items_can_be_for),
				'OBJ_type'=>new EE_Any_Foreign_Model_Name_Field('OBJ_type', __("Model Name this Line Item is for", "event_espresso"), true,null,$line_items_can_be_for),
			)
		);
 */
class EE_DMS_4_1_0_attendees extends EE_Data_Migration_Script_Stage_Table{
	private $_new_attendee_cpt_table;
	private $_new_attendee_meta_table;
	private $_new_reg_table;
	private $_new_transaction_table;
	private $_new_payment_table;
	private $_new_line_table;
	function __construct() {
		global $wpdb;
		$this->_pretty_name = __("Attendees", "event_espresso");
		$this->_old_table = $wpdb->prefix."events_attendee";
		$this->_new_attendee_cpt_table = $wpdb->posts;
		$this->_new_attendee_meta_table = $wpdb->prefix."esp_attendee_meta";
		$this->_new_reg_table = $wpdb->prefix."esp_registration";
		$this->_new_transaction_table = $wpdb->prefix."esp_transaction";
		$this->_new_payment_table = $wpdb->prefix."esp_payment";
		$this->_new_line_table = $wpdb->prefix."esp_line_item";
		parent::__construct();
	}
	
	protected function _migrate_old_row($old_row) {
		
		$new_att_id = $this->_insert_new_attendee_cpt($old_row);
		if( ! $new_att_id){
			//if we couldnt even make an attendee, abandon all hope
			return false;
		}
		$this->get_migration_script()->set_mapping($this->_old_table, $old_row['id'], $this->_new_attendee_cpt_table, $new_att_id);
		$new_att_meta_id = $this->_insert_attendee_meta_row($old_row, $new_att_id);
		if($new_att_meta_id){
			$this->get_migration_script()->set_mapping($this->_old_table, $old_row['id'], $this->_new_attendee_meta_table, $new_att_meta_id);
		}
		$txn_id = $this->_insert_new_transaction($old_row);
		if( ! $txn_id){
			//if we couldnt make the transaction, also abandon all hope
			return false;
		}
		$this->get_migration_script()->set_mapping($this->_old_table, $old_row['id'], $this->_new_transaction_table, $txn_id);
		$pay_id = $this->_insert_new_payment($old_row,$txn_id);
		if($pay_id){
			$this->get_migration_script()->set_mapping($this->_old_table,$old_row['id'],$this->_new_payment_table,$pay_id);
		}
		//even if there was no payment, we can go ahead with adding teh reg
		$new_regs = $this->_insert_new_registrations($old_row,$new_att_id,$txn_id);
		if($new_regs){
			$this->get_migration_script()->set_mapping($this->_old_table,$old_row['id'],$this->_new_reg_table,$new_regs);
		}
	}
	
	private function _insert_new_attendee_cpt($old_attendee){
		global $wpdb;
		$cols_n_values = array(
			'post_title'=>stripslashes($old_attendee['fname']." ".$old_attendee['lname']),//ATT_full_name
			'post_content'=>'',//ATT_bio
			'post_name'=>sanitize_title($old_attendee['fname']."-".$old_attendee['lname']),//ATT_slug
			'post_date'=>$this->get_migration_script()->convert_date_string_to_utc($this,$old_attendee,$old_attendee['date']),//ATT_created
			'post_excerpt'=>'',//ATT_short_bio
			'post_modified'=>$this->get_migration_script()->convert_date_string_to_utc($this,$old_attendee,$old_attendee['date']),//ATT_modified
			'post_author'=>0,//ATT_author
			'post_parent'=>0,//ATT_parent
			'post_type'=>'espresso_attendees',//post_type
			'post_status'=>'publish'//status
		);
		$datatypes = array(
			'%s',//ATT_full_name
			'%s',//ATT_bio
			'%s',//ATT_slug
			'%s',//ATT_created
			'%s',//ATT_short_bio
			'%s',//ATT_modified
			'%d',//ATT_author
			'%d',//ATT_parent
			'%s',//post_type
			'%s',//status
		);
		$success = $wpdb->insert($this->_new_attendee_cpt_table,$cols_n_values,$datatypes);
		if ( ! $success){
			$this->add_error($this->get_migration_script()->_create_error_message_for_db_insertion($this->_old_table, $old_attendee, $this->_new_attendee_cpt_table, $cols_n_values, $datatypes));
			return 0;
		}
		$new_id = $wpdb->insert_id;
		return $new_id;
	}
	
	private function _insert_attendee_meta_row($old_attendee,$new_attendee_cpt_id){
		global $wpdb;
		//get the state and country ids from the old row
		try{
			$new_country = $this->get_migration_script()->get_or_create_country(stripslashes($old_attendee['country_id']));
			$new_country_iso = $new_country['CNT_ISO'];
		}catch(EE_Error $exception){
			$new_country_iso = $this->get_migration_script()->get_default_country_iso();
		}
		try{
			$new_state = $this->get_migration_script()->get_or_create_state(stripslashes($old_attendee['state']),$new_country_iso);
			$new_state_id = $new_state['STA_ID'];
		}catch(EE_Error $exception){
			$new_state_id = 0;
		}
		$cols_n_values = array(
			'ATT_ID'=>$new_attendee_cpt_id,
			'ATT_fname'=>stripslashes($old_attendee['fname']),
			'ATT_lname'=>stripslashes($old_attendee['lname']),
			'ATT_address'=>stripslashes($old_attendee['address']),
			'ATT_address2'=>stripslashes($old_attendee['address2']),
			'ATT_city'=>stripslashes($old_attendee['city']),
			'STA_ID'=>$new_state_id,
			'CNT_ISO'=>$new_country_iso,
			'ATT_zip'=>stripslashes($old_attendee['zip']),
			'ATT_email'=>stripslashes($old_attendee['email']),
			'ATT_phone'=>stripslashes($old_attendee['phone']),			
		);
		$datatypes = array(
			'%d',//ATT_ID
			'%s',//ATT_fname
			'%s',//ATT_lname
			'%s',//ATT_address
			'%s',//ATT_address2
			'%s',//ATT_city
			'%d',//STA_ID
			'%s',//CNT_ISO
			'%s',//ATT_zip
			'%s',//ATT_email
			'%s',//ATT_phone
		);
		$success = $wpdb->insert($this->_new_attendee_meta_table,$cols_n_values,$datatypes);
		if ( ! $success){
			$this->add_error($this->get_migration_script()->_create_error_message_for_db_insertion($this->_old_table, $old_attendee, $this->_new_attendee_meta_table, $cols_n_values, $datatypes));
			return 0;
		}
		$new_id = $wpdb->insert_id;
		return $new_id;
	}
	
	/**
	 * Note: we don't necessarily create a new transaction for each attendee row.
	 * Only if the old attendee 'is_primary' is true; otherwise we find the old attendee row that
	 * 'is_primary' and has the same 'txn_id', then we return ITS new transaction id
	 * @global type $wpdb
	 * @param type $old_attendee
	 * @return int new transaction id
	 */
	private function _insert_new_transaction($old_attendee){
		global $wpdb;
		if(intval($old_attendee['is_primary'])){//primary attendee, so create txn
			
			//maps 3.1 payment stati onto 4.1 transaction stati
			$txn_status_mapping = array(
				'Completed'=>'TCM',
				'Pending'=>'TPN',
				'Payment Declined'=>'TIN',
				'Incomplete'=>'TIN',
				'Not Completed'=>'TIN',
				'Cancelled'=>'TIN',
				'Declined'=>'TIN'
			);
			$STS_ID = isset($txn_status_mapping[$old_attendee['payment_status']]) ? $txn_status_mapping[$old_attendee['payment_status']] : 'TIN';
			$cols_n_values = array(
				'TXN_timestamp'=>$this->get_migration_script()->convert_date_string_to_utc($this,$old_attendee,$old_attendee['date']),
				'TXN_total'=>floatval($old_attendee['total_cost']),
				'TXN_paid'=>floatval($old_attendee['amount_pd']),
				'STS_ID'=>$STS_ID,
				'TXN_hash_salt'=>$old_attendee['hashSalt']
			);
			$datatypes = array(
				'%s',//TXN_timestamp
				'%f',//TXN_total
				'%f',//TXN_paid
				'%s',//STS_ID
				'%s',//TXN_hash_salt
			);
			$success = $wpdb->insert($this->_new_transaction_table,$cols_n_values,$datatypes);
			if ( ! $success){
				$this->add_error($this->get_migration_script()->_create_error_message_for_db_insertion($this->_old_table, $old_attendee, $this->_new_transaction_table, $cols_n_values, $datatypes));
				return 0;
			}
			$new_id = $wpdb->insert_id;
			return $new_id;
		}else{//non-primary attendee, so find its primary attendee's transaction
			$primary_attendee_old_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM ".$this->_old_table." WHERE is_primary=1 and txn_id=%s",$old_attendee['txn_id']));
			$txn_id = $this->get_migration_script()->get_mapping_new_pk($this->_old_table, intval($primary_attendee_old_id), $this->_new_transaction_table);
			if( ! $txn_id){
				$this->add_error(sprintf(__("Could not find primary attendee's new transaction. Current attendee is: %s, we think the 3.1 primary attendee for it has id %d, but there's no 4.1 transaction for that primary attendee id.", "event_espresso"),  http_build_query($old_attendee),$primary_attendee_old_id));
				$txn_id = 0;
			}
			return $txn_id;
		}
		
		
	}
	/**
	 * Adds however many rgistrations are indicated by the old attendee's QUANTITY field,
	 * and returns an array of their IDs
	 * @global type $wpdb
	 * @param array $old_attendee
	 * @param int $new_attendee_id
	 * @param int $new_txn_id
	 * @return array of new registratio ids
	 */
	private function _insert_new_registrations($old_attendee,$new_attendee_id,$new_txn_id){
		global $wpdb;
		$reg_status_mapping = array(
				'Completed'=>'RAP',
				'Pending'=>'RPN',
				'Payment Declined'=>'RNA',
				'Incomplete'=>'RNA',
				'Not Completed'=>'RNA',
				'Cancelled'=>'RCN',
				'Declined'=>'RNA'
			);
		$STS_ID = isset($reg_status_mapping[$old_attendee['payment_status']]) ? $reg_status_mapping[$old_attendee['payment_status']] : 'RNA';
		$new_event_id = $this->get_migration_script()->get_mapping_new_pk($wpdb->prefix.'events_detail', $old_attendee['event_id'], $wpdb->posts);
		if( ! $new_event_id){
			$this->add_error(sprintf(__("Could not find NEW event CPT ID for old event '%d' on old attendee %s", "event_espresso"),$old_attendee['event_id'],http_build_query($old_attendee)));
		}
		
		$ticket_id = $this->_try_to_find_new_ticket_id($old_attendee,$new_event_id);
		if( ! $ticket_id){
			$this->add_error(sprintf(__("Could not find a NEW ticket for OLD attendee %s", "event_espresso"),http_build_query($old_attendee)));
		}
		$regs_on_this_row = intval($old_attendee['quantity']);
		$new_regs = array();
		for($count = 0; $count < $regs_on_this_row; $count++){
			$regs_on_this_event_and_txn = $this->_count_new_registrations_on_txn($new_txn_id) + 1;
			$cols_n_values = array(
				'EVT_ID'=>$new_event_id,
				'ATT_ID'=>$new_attendee_id,
				'TXN_ID'=>$new_txn_id,
				'TKT_ID'=>$ticket_id,
				'STS_ID'=>$STS_ID,
				'REG_date'=>$this->get_migration_script()->convert_date_string_to_utc($this,$old_attendee,$old_attendee['date']),
				'REG_final_price'=>$old_attendee['final_price'],
				'REG_session'=>$old_attendee['attendee_session'],
				'REG_code'=>$old_attendee['registration_id'],
				'REG_url_link'=>$old_attendee['registration_id'].'-'.$count,
				'REG_count'=>$regs_on_this_event_and_txn,
				'REG_group_size'=>$this->_sum_old_attendees_with_registration_id($old_attendee['registration_id']),
				'REG_att_is_going'=>true,
				'REG_deleted'=>false
			);
			$datatypes = array(
				'%d',//EVT_ID
				'%d',//ATT_ID
				'%d',//TXN_ID
				'%d',//TKT_ID
				'%s',//STS_ID
				'%s',//REG_date
				'%f',//REG_final_price
				'%s',//REG_session
				'%s',//REG_code
				'%s',//REG_url_link
				'%d',//REG_count
				'%d',//REG_group_size
				'%d',//REG_att_is_going
				'%d',//REG_deleted
			);
			$success = $wpdb->insert($this->_new_reg_table,$cols_n_values,$datatypes);
			if ( ! $success){
				$this->add_error($this->get_migration_script()->_create_error_message_for_db_insertion($this->_old_table, $old_attendee, $this->_new_reg_table, $cols_n_values, $datatypes));
				return 0;
			}
			$cols_n_values['REG_ID'] = $wpdb->insert_id;
			$new_regs[] = $wpdb->insert_id;
		}
		return $new_regs;
	}
	/**
	 * Makes a best guess at which ticket is the one the attendee purchased.
	 * Obviously, the old attendee's event_id narrows it down quite a bit;
	 * then the old attendee's orig_price and event_time, and price_option can uniquely identify the ticket
	 * however, if we don't find an exact match, see if any of those conditions match;
	 * and lastly if none of that works, just use the first ticket for the event we find
	 * @param array $old_attendee
	 */
	private function _try_to_find_new_ticket_id($old_attendee,$new_event_id){
		global $wpdb;
		$tickets_table = $wpdb->prefix."esp_ticket";
		$datetime_tickets_table = $wpdb->prefix."esp_datetime_ticket";
		$datetime_table = $wpdb->prefix."esp_datetime";
		
		$old_att_price_option = $old_attendee['price_option'];
		$old_att_price = floatval($old_attendee['orig_price']);
		
		$old_att_start_date = $old_attendee['start_date'];
		$old_att_start_time = $this->get_migration_script()->convertTimeFromAMPM($old_attendee['event_time']);
		$old_att_datetime = $this->get_migration_script()->convert_date_string_to_utc($this,$old_attendee,"$old_att_start_date $old_att_start_time:00");
		//add all conditions to an array from which we can SHIFT conditions off in order to widen our search
		//the most important condition should be last, as it will be array_shift'ed off last
		$conditions = array(
			$wpdb->prepare("$datetime_table.DTT_EVT_start = %s",$old_att_datetime),//times match?
			$wpdb->prepare("$tickets_table.TKT_price = %f",$old_att_price),//prices match?
			$wpdb->prepare("$tickets_table.TKT_name = %s",$old_att_price_option),//names match?
			$wpdb->prepare("$datetime_table.EVT_ID = %d",$new_event_id),//events match?
		);
		$select_and_join_part = "SELECT $tickets_table.TKT_ID FROM $tickets_table INNER JOIN 
			$datetime_tickets_table ON $tickets_table.TKT_ID = $datetime_tickets_table.TKT_ID INNER JOIN
			$datetime_table ON $datetime_tickets_table.DTT_ID = $datetime_table.DTT_ID";
		//start running queries, widening search each time by removing a condition
		do{
			$full_query = $select_and_join_part." WHERE ".implode(" AND ",$conditions)." LIMIT 1";
			$ticket_id_found = $wpdb->get_var($full_query);
			array_shift($conditions);
		}while( ! $ticket_id_found && $conditions);
		return $ticket_id_found;
		
	}
	/**
	 * Sums all the OLD registration with the $old_registration_id. This takes into account BOTH
	 * when each row has a quantity of 1, and when a single row has a quantity greater than 1
	 * @global type $wpdb
	 * @param type $old_registration_id
	 * @return int
	 */
	private function _sum_old_attendees_with_registration_id($old_registration_id){
		global $wpdb;
		$count = $wpdb->get_var($wpdb->prepare("SELECT SUM(quantity) FROM ".$this->_old_table." WHERE registration_id=%s",$old_registration_id));
		return intval($count);
	}
	/**
	 * Counts all the registrations on this transaction added SO FAR
	 * @global type $wpdb
	 * @param int $txn_id
	 * @return int
	 */
	private function _count_new_registrations_on_txn($txn_id){
		global $wpdb;
		$count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(REG_ID) FROM ".$this->_new_reg_table." WHERE TXN_ID=%d",$txn_id));
		return intval($count);
	}
	private function _insert_new_payment($old_attendee,$new_txn_id){
		global $wpdb;
		//only add a payment for primary attendees
		$old_pay_stati_indicating_no_payment = array('Pending','Incomplete','Not Completed');
		//if this is for a primary 3.1 attendee which WASN'T free and has a completed, cancelled, or declined payment...
		if(intval($old_attendee['is_primary']) && floatval($old_attendee['total_cost']) && !in_array($old_attendee['payment_status'], $old_pay_stati_indicating_no_payment)){
			$pay_status_mapping = array(
				'Completed'=>'PAP',
				'Payment Declined'=>'PDC',
				'Cancelled'=>'PCN',
				'Declined'=>'PDC'
			);
			$STS_ID = isset($pay_status_mapping[$old_attendee['payment_status']]) ? $pay_status_mapping[$old_attendee['payment_status']] : 'PFL';//IE, if we don't recognize teh status, assume paymetn failed
			$cols_n_values = array(
				'TXN_ID'=>$new_txn_id,
				'STS_ID'=>$STS_ID,
				'PAY_timestamp'=>$this->get_migration_script()->convert_date_string_to_utc($this,$old_attendee,$old_attendee['date']),
				'PAY_gateway'=>$old_attendee['txn_type'],
				'PAY_gateway_response'=>'',
				'PAY_txn_id_chq_nmbr'=>$old_attendee['txn_id'],
				'PAY_via_admin'=>false,
				'PAY_details'=>$old_attendee['transaction_details']
				
			);
			$datatypes = array(
				'%d',//TXN_Id
				'%s',//STS_ID
				'%s',//PAY_timestamp
				'%s',//PAY_gateway
				'%s',//PAY_gateway_response
				'%d',//PAY_via_admin
				'%s',//PAY_details
			);
			$success = $wpdb->insert($this->_new_payment_table,$cols_n_values,$datatypes);
			if ( ! $success){
				$this->add_error($this->get_migration_script()->_create_error_message_for_db_insertion($this->_old_table, $old_attendee, $this->_new_attendee_cpt_table, $cols_n_values, $datatypes));
				return 0;
			}
			$new_id = $wpdb->insert_id;
			return $new_id;
			
		}else{
			return 0;
		}
		
		
	}
	
}