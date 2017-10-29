<?php
use Phalcon\Mvc\Micro;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\Digit as DigitValidator;
use Phalcon\Validation\Validator\StringLength as StringLength;
use Phalcon\Validation\Validator\Alpha as AlphaValidator;
class UsersController extends \Phalcon\Mvc\Controller {
	public function index() {
	}
	/**
	 * Fetch all Record from database :-
	 */
	public function viewall() {
		$users = Users::find ();
		if ($users) :
			return Json_encode ( $users );
		 else :
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'Message' => 'Faield' 
			] );
		endif;
	}
	
	/*
	 * Fetch Record from database based on ID :-
	 */
	public function getmyaccountinfo() {
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
		$user_info = $this->tokenvalidate->getuserinfo ( $headers ['Token'], $baseurl );
		$input_data = $this->request->getJsonRawBody ();
		$id = isset ( $user_info->user_info->id ) ? $user_info->user_info->id : '';
		if (empty ( $id )) {
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Id is null'
			] );
		}
			$users = Users::findFirstByid ( $id );
			if ($users) :
			$userinfo [$users->parent_type] = $users;
			$map = ParentsMappingProfiles::findFirstByprimary_parents_id ( $id );
			if (! empty ( $map->secondary_parent_id )) {
				$secuser = Users::findFirstByid ( $map->secondary_parent_id );
				$userinfo [$secuser->parent_type] = $secuser;
			}
			return $this->response->setJsonContent ( $userinfo );
		 
			 else :
				return $this->response->setJsonContent ( [ 
						'status' => false,
						'Message' => 'Data not found' 
				] );
			endif;
	}
	
	/**
	 * This function using to create NidaraParentsProfile information
	 */
	public function save() {
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
		if (empty ( $input_data )) {
			return $this->response->setJsonContent ( [ 
					"status" => false,
					"message" => "Please give the input datas" 
			] );
		}
		/**
		 * This object using valitaion
		 */
		if (! empty ( $input_data->father )) {
			$validation = new Validation ();
			$validation->add ( 'first_name', new PresenceOf ( [ 
					'message' => 'first name is required' 
			] ) );
			$validation->add ( 'last_name', new PresenceOf ( [ 
					'message' => 'last name is required' 
			] ) );
			$validation->add ( [ 
					"first_name",
					"last_name" 
			], new AlphaValidator ( [ 
					"message" => [ 
							"first_name" => "First name must contain only  letters",
							"last_name " => "Last name must contain only letters" 
					] 
			] ) );
			$validation->add ( [ 
					"first_name",
					"last_name" 
			], new StringLength ( [ 
					"max" => [ 
							"first_name" => 20,
							"last_name" => 30 
					],
					"min" => [ 
							"first_name" => 4,
							"last_name" => 2 
					],
					"messageMaximum" => [ 
							"first_name" => "We don't like really long firstnames",
							"last_name" => "We don't like really long last names" 
					],
					"messageMinimum" => [ 
							"name_last" => "We don't like too short first names",
							"name_first" => "We don't like too short last names" 
					] 
			] ) );
			$validation->add ( 'email', new PresenceOf ( [ 
					'message' => 'email is required' 
			] ) );
			$validation->add ( 'email', new Email ( [ 
					'message' => 'The e-mail is not valid' 
			] ) );
			$validation->add ( 'mobile', new PresenceOf ( [ 
					'message' => 'mobile number is required' 
			] ) );
			$validation->add ( "mobile", new DigitValidator ( [ 
					"message" => "mobile number field must be numeric" 
			] ) );
			$validation->add ( 'mobile', new StringLength ( [ 
					"max" => 10,
					"min" => 10,
					"messageMaximum" => "The Mobile Number must be 10 digits long",
					"messageMinimum" => "The Mobile Number must be 10 digits long" 
			] ) );
			$validation->add ( 'occupation', new PresenceOf ( [ 
					'message' => 'occupation is required' 
			] ) );
			$validation->add ( 'company_name', new PresenceOf ( [ 
					'message' => 'company name is required']));
        $messages = $validation->validate ( $input_data->father );
		if (count ( $messages )) {
				foreach ( $messages as $message ) {
					$result [] = $message->getMessage ();
				}
				return $this->response->setJsonContent ( $result );
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
		foreach ( $input_data as $key => $userinfo ) {
			if ($userinfo->id) {
				$users = Users::findFirstByid ( $userinfo->id );
			} else {
				$users = new Users ();
				$users->id = $this->parentsidgen->getNewId ( "users" );
			}
			$users->parent_type = $key;
			$users->user_type = 'parent';
			$users->first_name = $userinfo->first_name;
			$users->last_name = $userinfo->last_name;
			$users->email = $userinfo->email;
			$users->mobile = $userinfo->mobile;
			$users->occupation = $userinfo->occupation;
			$users->company_name = $userinfo->company_name;
			$users->created_at = date ( 'Y-m-d H:i:s' );
			$users->created_by = $user->id;
			$users->modified_at = date ( 'Y-m-d H:i:s' );
			$users->status = 1;
			$users->save ();
			if (! isset ( $userinfo->id )) {
				$parents_map = ParentsMappingProfiles::findFirstByprimary_parents_id ( $user->id );
				if ($parents_map) {
					$parents_map->secondary_parent_id = $users->id;
					$parents_map->secondary_parent_type = $key;
					$parents_map->save ();
				}
			}
		}
		return $this->response->setJsonContent ( [ 
				'status' => true,
				'message' => 'saved successfully' 
		] );
	}
	
	/**
	 * This function using to NidaraParentsProfile information edit
	 */
	public function update($id = null) {
		$input_data = $this->request->getJsonRawBody ();
		$id = isset ( $input_data->mother_id ) ? $input_data->mother_id : '';
		if (empty ( $id )) :
			return $this->response->setJsonContent ( [ 
					'status' => 'Error',
					'message' => 'Id is null' 
			] );
		 else :
			$validation = new Validation ();
			
			$validation->add ( 'first_name', new PresenceOf ( [ 
					'message' => 'first name is required' 
			] ) );
			$validation->add ( 'last_name', new PresenceOf ( [ 
					'message' => 'last name is required' 
			] ) );
			$validation->add ( [ 
					"first_name",
					"last_name" 
			], new AlphaValidator ( [ 
					"message" => [ 
							"first_name" => "First name must contain only  letters",
							"last_name " => "Last name must contain only letters" 
					] 
			] ) );
			$validation->add ( [ 
					"first_name",
					"last_name" 
			], new StringLength ( [ 
					"max" => [ 
							"first_name" => 20,
							"last_name" => 30 
					],
					"min" => [ 
							"first_name" => 4,
							"last_name" => 2 
					],
					"messageMaximum" => [ 
							"first_name" => "We don't like really long firstnames",
							"last_name" => "We don't like really long last names" 
					],
					"messageMinimum" => [ 
							"name_last" => "We don't like too short first names",
							"name_first" => "We don't like too short last names" 
					] 
			] ) );
			$validation->add ( 'email', new PresenceOf ( [ 
					'message' => 'email is required' 
			] ) );
			$validation->add ( 'email', new Email ( [ 
					'message' => 'The e-mail is not valid' 
			] ) );
			$validation->add ( 'mobile', new PresenceOf ( [ 
					'message' => 'mobile number is required' 
			] ) );
			$validation->add ( "mobile", new DigitValidator ( [ 
					"message" => "mobile number field must be numeric" 
			] ) );
			$validation->add ( 'mobile', new StringLength ( [ 
					"max" => 10,
					"min" => 10,
					"messageMaximum" => "The Mobile Number must be 10 digits long",
					"messageMinimum" => "The Mobile Number must be 10 digits long" 
			] ) );
			$validation->add ( 'occupation', new PresenceOf ( [ 
					'message' => 'occupation is required' 
			] ) );
			$validation->add ( 'company_name', new PresenceOf ( [ 
					'message' => 'company nameis required' 
			] ) );
			
			$messages = $validation->validate ( $input_data->mother_info );
			if (count ( $messages )) :
				foreach ( $messages as $message ) :
					$result [] = $message->getMessage ();
				endforeach
				;
				return $this->response->setJsonContent ( $result );
			 else :
				$user = Users::findFirstByid ( $id );
				if ($user) :
					$user->id = $input_data->mother_id;
					$user->parent_type = 'mother';
					$user->user_type = 'parent';
					$user->first_name = $input_data->mother_info->first_name;
					$user->last_name = $input_data->mother_info->last_name;
					$user->email = $input_data->mother_info->email;
					$user->mobile = $input_data->mother_info->mobile;
					$user->occupation = $input_data->mother_info->occupation;
					$user->company_name = $input_data->mother_info->company_name;
					
					$user->created_by = $input_data->mother_id;
					$user->modified_at = date ( 'Y-m-d H:i:s' );
					$user->country_of_residence = $input_data->mother_info->country_id;
					$user->country_of_citizenship = $input_data->mother_info->citizen_id;
					$user->save ();
				
                endif;
				$id = isset ( $input_data->father_id ) ? $input_data->father_id : '';
				if (empty ( $id )) :
					return $this->response->setJsonContent ( [ 
							'status' => 'Error',
							'message' => 'Id is null' 
					] );
				 else :
					$validation = new Validation ();
					$validation->add ( 'first_name', new PresenceOf ( [ 
							'message' => 'first name is required' 
					] ) );
					$validation->add ( 'last_name', new PresenceOf ( [ 
							'message' => 'last name is required' 
					] ) );
					$validation->add ( [ 
							"first_name",
							"last_name" 
					], new AlphaValidator ( [ 
							"message" => [ 
									"first_name" => "First name must contain only  letters",
									"last_name " => "Last name must contain only letters" 
							] 
					] ) );
					$validation->add ( [ 
							"first_name",
							"last_name" 
					], new StringLength ( [ 
							"max" => [ 
									"first_name" => 20,
									"last_name" => 30 
							],
							"min" => [ 
									"first_name" => 4,
									"last_name" => 2 
							],
							"messageMaximum" => [ 
									"first_name" => "We don't like really long firstnames",
									"last_name" => "We don't like really long last names" 
							],
							"messageMinimum" => [ 
									"name_last" => "We don't like too short first names",
									"name_first" => "We don't like too short last names" 
							] 
					] ) );
					$validation->add ( 'email', new PresenceOf ( [ 
							'message' => 'email is required' 
					] ) );
					$validation->add ( 'email', new Email ( [ 
							'message' => 'The e-mail is not valid' 
					] ) );
					$validation->add ( 'mobile', new PresenceOf ( [ 
							'message' => 'mobile number is required' 
					] ) );
					$validation->add ( "mobile", new DigitValidator ( [ 
							"message" => "mobile number field must be numeric" 
					] ) );
					$validation->add ( 'mobile', new StringLength ( [ 
							"max" => 10,
							"min" => 10,
							"messageMaximum" => "The Mobile Number must be 10 digits long",
							"messageMinimum" => "The Mobile Number must be 10 digits long" 
					] ) );
					$validation->add ( 'occupation', new PresenceOf ( [ 
							'message' => 'occupation is required' 
					] ) );
					$validation->add ( 'company_name', new PresenceOf ( [ 
							'message' => 'company nameis required' 
					] ) );
					
					$messages = $validation->validate ( $input_data->father_info );
					if (count ( $messages )) :
						foreach ( $messages as $message ) :
							$result [] = $message->getMessage ();
						endforeach;
						return $this->response->setJsonContent ( $result );
					 else :
						$users = Users::findFirstByid ( $id );
						if ($users) :
							$users->id = $input_data->father_id;
							$users->parent_type = 'father';
							$users->user_type = 'parent';
							$users->first_name = $input_data->father_info->first_name;
							$users->last_name = $input_data->father_info->last_name;
							$users->email = $input_data->father_info->email;
							$users->mobile = $input_data->father_info->mobile;
							$users->occupation = $input_data->father_info->occupation;
							$users->company_name = $input_data->father_info->company_name;
							
							$users->created_by = $input_data->father_id;
							$users->modified_at = date ( 'Y-m-d H:i:s' );
							$users->country_of_residence = $input_data->father_info->country_id;
							$users->country_of_citizenship = $input_data->father_info->citizen_id;
							if ($users->save ()) :
								return $this->response->setJsonContent ( [ 
										'status' => true,
										'message' => 'successfully' 
								] );
							 else :
								return $this->response->setJsonContent ( [ 
										'status' => false,
										'message' => 'Failed' 
								] );
							endif;
						 else :
							return $this->response->setJsonContent ( [ 
									'status' => false,
									'message' => 'Invalid id' 
							] );
						endif;
					endif;
				endif;
			endif;
		endif;
	}
	
	/**
	 * This function using delete kids caregiver information
	 */
	public function delete() {
		$input_data = $this->request->getJsonRawBody ();
		$id = isset ( $input_data->mother_id ) ? $input_data->mother_id : '';
		if (empty ( $id )) :
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Id is null' 
			] );
		 else :
			$users_mom = Users::findFirstByid ( $id );
			if ($users_mom) :
				$users_mom->delete ();
				$id = isset ( $input_data->father_id ) ? $input_data->father_id : '';
				if (empty ( $id )) :
					return $this->response->setJsonContent ( [ 
							'status' => false,
							'message' => 'Id is null' 
					] );
				 else :
					$users = Users::findFirstByid ( $id );
					if ($users) :
						if ($users->delete ()) :
							return $this->response->setJsonContent ( [ 
									'status' => true,
									'Message' => 'Record has been deleted successfully ' 
							] );
						 else :
							return $this->response->setJsonContent ([
									'status' => false,
									'Message' => 'Data could not be deleted'
							] );
						endif;
					 else :
						return $this->response->setJsonContent ( [ 
								'status' => false,
								'Message' => 'ID doesn\'t' 
						] );
					endif;
				endif;
			
			
			
            endif;
		endif;
	}
	
	/**
	 * Country updation by kid id
	 */
	public function countryupdatebyuserid() {
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
			$user_info=$this->tokenvalidate->getuserinfo ( $headers ['Token'], $baseurl );
			$input_data = $this->request->getJsonRawBody ();
			$id = isset ( $user_info->user_info->id ) ? $user_info->user_info->id : '';
			if (empty ( $id )) {
				return $this->response->setJsonContent ( [ 
						'status' => false,
						'message' => 'Id is null' 
				] );
			}
			$validation = new Validation ();
			$validation->add ( 'country_residence_id', new PresenceOf ( [ 
					'message' => 'Country of residence is required' 
			] ) );
			$validation->add ( 'country_citizen_id', new PresenceOf ( [ 
					'message' => 'Country of citizen is required' 
			] ) );
			$messages = $validation->validate ( $input_data );
			if (count ( $messages )) {
				foreach ( $messages as $message ) {
					$result [] = $message->getMessage ();
				}
				return $this->response->setJsonContent ( $result );
			}
			
			$users = Users::findFirstByid ( $id );
			if ($users) :
				$users->country_of_residence = $input_data->country_residence_id;
				$users->country_of_citizenship = $input_data->country_citizen_id;
				if ($users->save ()) :
					return $this->response->setJsonContent ( [ 
							'status' => true,
							'message' => 'Country updated successfully' 
					] );
				 else :
					return $this->response->setJsonContent ( [ 
							'status' => false,
							'message' => 'Failed' 
					] );
				endif;
			 
			 else :
				return $this->response->setJsonContent ([ 
						'status' => false,
						'message' => 'Invalid id' 
				]);
			endif;
			} catch ( Exception $e ) {
				return $this->response->setJsonContent ( [
						'status' => false,
						'message' => 'Cannot update country details'
				] );
			}
	}
	public function getcountryinfo(){
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
		$user = Users::findFirstByid ( $id );
		if (! empty ( $user )) {
			$country ['country_of_residence'] = $user->country_of_residence;
			$country ['country_of_citizenship'] = $user->country_of_citizenship;
			$country ['user_id'] = $user->id;
			
		}
		return $this->response->setJsonContent ( [ 
				$country 
		] );
	}
}
