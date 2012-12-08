<?php

abstract class EE_Offline_Gateway extends EE_Gateway {


	protected function __construct(EEM_Gateways &$model) {
		parent::__construct($model);
	}

	/**
	 * 		process_gateway_selection()
	 * 		@access public
	 * 		@return 	mixed	array on success or FALSE on fail
	 */
	public function process_gateway_selection() {	
		global $espresso_notices;
		$espresso_notices['success'][] = __('Off-line gateway selected', 'event_espresso');
	}



	/**
	 * 		set_billing_info_for_confirmation
	 * 		@access public
	 * 		@param array	$billing_info
	 * 		@return array
	 */
	public function set_billing_info_for_confirmation( $billing_info ) {
		$confirm_data = array();
		$confirm_data['gateway'] = $this->_EEM_Gateways->display_name();
		return $confirm_data;
	}

	public function set_transaction_details() {
		$txn_results = array(
				'gateway' => $this->_payment_settings['display_name'],
				'approved' => FALSE,
				'response_msg' => __('You\'re registration will be marked as complete once your payment is received.', 'event_espresso'),
				'status' => 'Incomplete',
				'raw_response' => serialize($_REQUEST),
				'amount' => 0.00,
				'method' => 'Off-line',
				'auth_code' => '',
				'md5_hash' => '',
				'invoice_number' => '',
				'transaction_id' => ''
		);
		global $EE_Session;
		$EE_Session->set_session_data(array('txn_results' => $txn_results), 'session_data');
		$session = $EE_Session->get_session_data();
//		printr( $session, 'session data ( ' . __FUNCTION__ . ' on line: ' .  __LINE__ . ' )' ); die();
//		die();	
		require_once ( EVENT_ESPRESSO_INCLUDES_DIR . 'models/EEM_Transaction.model.php' );
		$transaction = $session['transaction'];
		$transaction->set_paid($txn_results['amount']);
		$transaction->set_details( $txn_results );
		$txn_status = 'TPN';
		$transaction->set_status($txn_status);
		unset( $session['transaction'] );
		$transaction->set_txn_session_data( $session );
		if (isset($session['taxes'])) {
			$tax_data = array('taxes' => $session['taxes'], 'tax_totals' => $session['tax_totals']);
			$transaction->set_tax_data($tax_data);
		}
		$transaction->update();
	}
	

}