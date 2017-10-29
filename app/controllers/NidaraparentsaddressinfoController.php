<?php

use Phalcon\Mvc\Micro;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Digit;
use Phalcon\Validation\Validator\Alpha;
use Phalcon\Validation\Validator\Date;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\PresenceOf;

class NidaraparentsaddressinfoController extends \Phalcon\Mvc\Controller {

    public function index() {

    }

    /**
     * Fetch all Record from database :-
     */
    public function viewall() {
        $parentsaddress_view = NidaraParentsAddressInfo::find();
        if ($parentsaddress_view):
            return Json_encode($parentsaddress_view);
        else:
            return $this->response->setJsonContent(['status' => false, 'Message' => 'Failed']);
        endif;
    }

    /*
     * Fetch Record from database based on ID :-
     */

    public function getbyid() {

        $input_data = $this->request->getJsonRawBody();
        $id = isset($input_data->id) ? $input_data->id : '';
        if (empty($id)):
            return $this->response->setJsonContent(['status' => false, 'message' => 'Invalid input parameter']);
        else:
            $parentsaddress_getbyid = NidaraParentsAddressInfo::findFirstByid($id);
            if ($parentsaddress_getbyid):
                return Json_encode($parentsaddress_getbyid);
            else:
                return $this->response->setJsonContent(['status' => false, 'Message' => 'Data not found']);
            endif;
        endif;
    }

    public function getaddressinfo(){
    	$headers = $this->request->getHeaders ();
    	if (empty ( $headers ['Token'] )) {
    		return $this->response->setJsonContent ( [
    				"status" => false,
    				"message" => "Please give the token"
    		] );
    	}
    	$baseurl = $this->config->baseurl;
    	$token_check = $this->tokenvalidate->tokencheck ( $headers ['Token'], $baseurl );
    	if ($token_check->status != 1) {
    		return $this->response->setJsonContent ( [
    				"status" => false,
    				"message" => "Invalid User"
    		] );
    	}
    	$user_info=$this->tokenvalidate->getuserinfo ( $headers['Token'], $baseurl );
    	$input_data = $this->request->getJsonRawBody ();
    	$id = isset ( $user_info->user_info->id ) ? $user_info->user_info->id : '';
    	if (empty ( $id )) {
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Id is null' 
			] );
		}
		$parentsaddress = NidaraParentsAddressInfo::findByusers_id ( $id );
		$parentsaddressinfo = array ();
		if (! empty ( $parentsaddress )) {
			foreach ( $parentsaddress as $address ) {
				if ($address->address_type == 'home_address') {
					$home_address = array ();
					foreach ( $address as $key => $value ) {
						$key = "home_" . $key;
						$home_address [$key] = $value;
					}
					$parentsaddressinfo ['home_address'] = $home_address;
				} elseif ($address->address_type == 'billing_address') {
					$billing_address = array ();
					foreach ( $address as $key => $value ) {
						$key = "billing_" . $key;
						$billing_address [$key] = $value;
					}
					$parentsaddressinfo ['billing_address'] = $billing_address;
				}
			}
		}
    	return $this->response->setJsonContent ( $parentsaddressinfo );
    }
    /**
     * This function using to create NidaraParentsAddressInfo information
     */
    public function create() {
		try {
			$headers = $this->request->getHeaders ();
			if (empty ( $headers ['Token'] )) {
				return $this->response->setJsonContent ( [ 
						"status" => false,
						"message" => "Please give the token" 
				] );
			}
			$baseurl = $this->config->baseurl;
			$token_check = $this->tokenvalidate->tokencheck ( $headers ['Token'], $baseurl );
			if ($token_check->status != 1) {
				return $this->response->setJsonContent ( [ 
						"status" => false,
						"message" => "Invalid User" 
				] );
			}
			$input_data = $this->request->getJsonRawBody ();
			/**
			 * This object using valitaion
			 */
			$validation = new Validation ();
			$validation->add ( 'home_address1', new PresenceOf ( [ 
					'message' => 'Home Address1 is required' 
			] ) );
			$validation->add ( 'home_address2', new PresenceOf ( [ 
					'message' => 'Home Address2 is required' 
			] ) );
			$validation->add ( 'home_town_city', new PresenceOf ( [ 
					'message' => 'Home Town city is required' 
			] ) );
			$validation->add ( 'home_pincode', new PresenceOf ( [ 
					'message' => 'Home Pincode is required' 
			] ) );
			$validation->add ( 'home_state', new PresenceOf ( [ 
					'message' => 'Home State is required' 
			] ) );
			$validation->add ( 'home_country', new PresenceOf ( [ 
					'message' => 'Home Country is required' 
			] ) );
			if (! empty ( $input_data->is_billing_same )) {
				$validation->add ( 'billing_address1', new PresenceOf ( [ 
						'message' => 'Billing Address1 is required' 
				] ) );
				$validation->add ( 'billing_address2', new PresenceOf ( [ 
						'message' => 'Billing Address2 is required' 
				] ) );
				$validation->add ( 'billing_town_city', new PresenceOf ( [ 
						'message' => 'Billing Town city is required' 
				] ) );
				$validation->add ( 'billing_pincode', new PresenceOf ( [ 
						'message' => 'Billing Pincode is required' 
				] ) );
				$validation->add ( 'billing_state', new PresenceOf ( [ 
						'message' => 'Billing State is required' 
				] ) );
				$validation->add ( 'billing_country', new PresenceOf ( [ 
						'message' => 'Billing Country is required' 
				] ) );
			}
			$messages = $validation->validate ( $input_data );
			if (count ( $messages )) :
				foreach ( $messages as $message ) :
					$result [] = $message->getMessage ();
				endforeach
				;
				return $this->response->setJsonContent ( $result );
			 else :
				$baseurl = $this->config->baseurl;
				$token_validate = $this->tokenvalidate->getuserinfo ( $headers ['Token'], $baseurl );
				if(empty($token_validate)){
					return $this->response->setJsonContent ( [
							'status' => false,
							'message' => 'Invalid User'
					] );
				}
				$username = $token_validate->user_info->email;
				$user = Users::findFirstByemail ( $username );
				$home_address = new NidaraParentsAddressInfo ();
				$home_address->id = $this->parentsidgen->getNewId ( "nidaraparentsaddress" );
				$home_address->address_type = "home_address";
				$home_address->users_id = $user->id;
				$home_address->address1 = $input_data->home_address1;
				$home_address->address2 = $input_data->home_address2;
				$home_address->town_city = $input_data->home_town_city;
				$home_address->pincode = $input_data->home_pincode;
				$home_address->state = $input_data->home_state;
				$home_address->country = $input_data->home_country;
				$home_address->created_at = date ( 'Y-m-d H:i:s' );
				$home_address->created_by = 1;
				$home_address->modified_at = date ( 'Y-m-d H:i:s' );
				$home_address->is_billing_same = $input_data->is_billing_same;
				if ($home_address->save ()) :
					$billing_address = new NidaraParentsAddressInfo ();
					$billing_address->id = $this->parentsidgen->getNewId ( "nidaraparentsaddress" );
					$billing_address->address_type = "billing_address";
					$billing_address->users_id = $user->id;
					if ($input_data->is_billing_same) {
						$billing_address->address1 = $input_data->home_address1;
						$billing_address->address2 = $input_data->home_address2;
						$billing_address->town_city = $input_data->home_town_city;
						$billing_address->pincode = $input_data->home_pincode;
						$billing_address->state = $input_data->home_state;
						$billing_address->country = $input_data->home_country;
					} else {
						$billing_address->address1 = $input_data->billing_address1;
						$billing_address->address2 = $input_data->billing_address2;
						$billing_address->town_city = $input_data->billing_town_city;
						$billing_address->pincode = $input_data->billing_pincode;
						$billing_address->state = $input_data->billing_state;
						$billing_address->country = $input_data->billing_country;
					}
					$billing_address->created_at = date ( 'Y-m-d H:i:s' );
					$billing_address->created_by = 1;
					$billing_address->modified_at = date ( 'Y-m-d H:i:s' );
					$billing_address->save();
					return $this->response->setJsonContent ( [ 
							'status' => true,
							'message' => 'Address information updated successfully' 
					] );
				 else :
					return $this->response->setJsonContent ( [ 
							'status' => false,
							'message' => 'Failed' 
					] );
				endif;
			endif;
		} catch ( Exception $e ) {
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Error while saving the datas' 
			] );
		}
	}

    /**
     * This function using to NidaraParentsAddressInfo information edit
     */
    public function update() {
    	try {
    		$headers = $this->request->getHeaders ();
    		if (empty ( $headers ['Token'] )) {
    			return $this->response->setJsonContent ( [
    					"status" => false,
    					"message" => "Please give the token"
    			] );
    		}
    		$baseurl = $this->config->baseurl;
    		$token_check = $this->tokenvalidate->tokencheck ( $headers ['Token'], $baseurl );
    		if ($token_check->status != 1) {
    			return $this->response->setJsonContent ( [
    					"status" => false,
    					"message" => "Invalid User"
    			] );
    		}
    		$input_data = $this->request->getJsonRawBody ();
    		$home = $input_data->home_address;
    		$billing = $input_data->home_address;
    		/**
    		 * This object using valitaion
    		*/
    		$validation = new Validation ();
    		$validation->add ( 'home_address1', new PresenceOf ( [
    				'message' => 'Home Address1 is required'
    		] ) );
    		$validation->add ( 'home_address2', new PresenceOf ( [
    				'message' => 'Home Address2 is required'
    		] ) );
    		$validation->add ( 'home_town_city', new PresenceOf ( [
    				'message' => 'Home Town city is required'
    		] ) );
    		$validation->add ( 'home_pincode', new PresenceOf ( [
    				'message' => 'Home Pincode is required'
    		] ) );
    		$validation->add ( 'home_state', new PresenceOf ( [
    				'message' => 'Home State is required'
    		] ) );
    		$validation->add ( 'home_country', new PresenceOf ( [
    				'message' => 'Home Country is required'
    		] ) );
    		$messages = $validation->validate ( $home );
    		if (count ( $messages )) {
				foreach ( $messages as $message ) {
					$result [] = $message->getMessage ();
				}
				return $this->response->setJsonContent ( $result );
			}
    		if (! empty ( $input_data->is_billing_same )) {
    			$bilvalidation = new Validation ();
    			$bilvalidation->add ( 'billing_address1', new PresenceOf ( [
    					'message' => 'Billing Address1 is required'
    			] ) );
    			$bilvalidation->add ( 'billing_address2', new PresenceOf ( [
    					'message' => 'Billing Address2 is required'
    			] ) );
    			$bilvalidation->add ( 'billing_town_city', new PresenceOf ( [
    					'message' => 'Billing Town city is required'
    			] ) );
    			$bilvalidation->add ( 'billing_pincode', new PresenceOf ( [
    					'message' => 'Billing Pincode is required'
    			] ) );
    			$bilvalidation->add ( 'billing_state', new PresenceOf ( [
    					'message' => 'Billing State is required'
    			] ) );
    			$bilvalidation->add ( 'billing_country', new PresenceOf ( [
    					'message' => 'Billing Country is required'
    			] ) );
    			$bilmessages = $bilvalidation->validate ( $home );
				if (count ( $bilmessages )) {
					foreach ( $bilmessages as $message ) {
						$bilresult [] = $message->getMessage ();
					}
					return $this->response->setJsonContent ( $bilresult );
				}
    		}
    		$baseurl = $this->config->baseurl;
    		$token_validate = $this->tokenvalidate->getuserinfo ( $headers ['Token'], $baseurl );
    		if(empty($token_validate)){
    			return $this->response->setJsonContent ( [
    					'status' => false,
    					'message' => 'Invalid User'
    			] );
    		}
    		$username = $token_validate->user_info->email;
    		$user = Users::findFirstByemail ( $username );
			$home_address = NidaraParentsAddressInfo::findFirstByid ( $input_data->home_id );
    		$home_address->users_id = $user->id;
    		$home_address->address1 = $home->home_address1;
    		$home_address->address2 = $home->home_address2;
    		$home_address->town_city = $home->home_town_city;
    		$home_address->pincode = $home->home_pincode;
    		$home_address->state = $home->home_state;
    		$home_address->country = $home->home_country;
    		$home_address->modified_at = date ( 'Y-m-d H:i:s' );
    		$home_address->is_billing_same = $input_data->is_billing_same;
    		$home_address->save ();
    		$billing_address = NidaraParentsAddressInfo::findFirstByid ( $input_data->billing_id );
    		$billing_address->users_id = $user->id;
    		if ($input_data->is_billing_same) {
    			$billing_address->address1 = $home->home_address1;
    			$billing_address->address2 = $home->home_address2;
    			$billing_address->town_city = $home->home_town_city;
    			$billing_address->pincode = $home->home_pincode;
    			$billing_address->state = $home->home_state;
    			$billing_address->country = $home->home_country;
    		} else {
    			$billing_address->address1 = $billing->billing_address1;
    			$billing_address->address2 = $billing->billing_address2;
    			$billing_address->town_city = $billing->billing_town_city;
    			$billing_address->pincode = $billing->billing_pincode;
    			$billing_address->state = $billing->billing_state;
    			$billing_address->country = $billing->billing_country;
    		}
    		$billing_address->created_at = date ( 'Y-m-d H:i:s' );
    		$billing_address->created_by = 1;
    		$billing_address->modified_at = date ( 'Y-m-d H:i:s' );
    		$billing_address->save();
    		return $this->response->setJsonContent ( [
    				'status' => true,
    				'message' => 'Address information updated successfully'
    		] );
    	} catch ( Exception $e ) {
    		return $this->response->setJsonContent ( [
    				'status' => false,
    				'message' => 'Error while saving the datas'
    		] );
    	}
    }

    /**
     * This function using delete kids caregiver information
     */
    public function delete() {

        $input_data = $this->request->getJsonRawBody();
        $id = isset($input_data->id) ? $input_data->id : '';
        if (empty($id)):
            return $this->response->setJsonContent(['status' => false, 'message' => 'Id is null']);
        else:
            $parentsaddress_delete = NidaraParentsAddressInfo::findFirstByid($id);
            if ($parentsaddress_delete):
                if ($parentsaddress_delete->delete()):
                    return $this->response->setJsonContent(['status' => true, 'Message' => 'Record has been deleted succefully ']);
                else:
                    return $this->response->setJsonContent(['status' => false, 'Message' => 'Data could not be deleted']);
                endif;
            else:
                return $this->response->setJsonContent(['status' => false, 'Message' => 'ID doesn\'t']);
            endif;
        endif;
    }

}
