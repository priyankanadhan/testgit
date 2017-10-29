<?php
use Phalcon\Mvc\Micro;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Aws\Credentials\CredentialProvider;
use Aws\Ses\SesClient;
use Aws\Ses\Exception\SesException;
require BASE_PATH.'/vendor/autoload.php';
class AccountstatusController extends \Phalcon\Mvc\Controller {
	public function index() {
	}
	/**
	 * Fetch all Record from database :-
	 */
	public function viewall() {
		$account = AccountStatus::find ();
		if ($account) :
			return Json_encode ( $account );
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
	public function getbyid($id = null) {
		$input_data = $this->request->getJsonRawBody ();
		$id = isset ( $input_data->id ) ? $input_data->id : '';
		if (empty ( $id )) :
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Invalid input parameter' 
			] );
		 else :
			$account = AccountStatus::findFirstByid ( $id );
			if ($account) :
				return Json_encode ( $account );
			 else :
				return $this->response->setJsonContent ( [ 
						'status' => false,
						'Message' => 'Data not found' 
				] );
			endif;
		endif;
	}
	/**
	 * This function using to create AccountStatus information
	 */
	public function deactivate() {
		$input_data = $this->request->getJsonRawBody ();
		$headers = $this->request->getHeaders ();
		if (empty ( $headers ['Token'] )) {
			return $this->response->setJsonContent ( [ 
					"status" => false,
					"message" => "Please give the token" 
			] );
		}
		if (empty ( $input_data )) {
			return $this->response->setJsonContent ( [ 
					"status" => false,
					"message" => "Please give the input datas" 
			] );
		}
		$baseurl = $this->config->baseurl;
		$token_check = $this->tokenvalidate->tokencheck ( $headers ['Token'], $baseurl);
		if ($token_check->status != 1) {
			return $this->response->setJsonContent ( [ 
					"status" => false,
					"message" => "Invalid User" 
			] );
		}
		
		/**
		 * This object using valitaion
		 */
		$validation = new Validation ();
		$validation->add ( 'password', new PresenceOf ( [
				'message' => 'Password is required'
		] ) );
		$validation->add ( 'why_are_you_leaving_id', new PresenceOf ( [ 
				'message' => 'reason for leaving is required' 
		] ) );
		$messages = $validation->validate ( $input_data );
		if (count ( $messages )) :
			foreach ( $messages as $message ) :
				$result [] = $message->getMessage ();
			endforeach;
			return $this->response->setJsonContent ( $result );
		 else :
			$token_validate = $this->tokenvalidate->usercheckbypassword ( $headers ['Token'], $baseurl,$input_data->password );
			$username = $token_validate->username;
			$user = Users::findFirstByemail ($username );
			$account = new AccountStatus ();
			$account->id = $this->parentsidgen->getNewId ( "account" );
			$account->elaboration = $input_data->elaboration;
			$account->users_id = $user->id;
			$account->why_are_you_leaving_id = $input_data->why_are_you_leaving_id;
			if ($account->save ()) :
				$this->deactivateMail ( $user );
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
		endif;
	}
	
	/**
	 * Deactivate account
	 */
	function deactivateMail($user) {
		$profile = 'default';
		$path = APP_PATH . '/library/credentials.ini';
		$provider = CredentialProvider::ini ( $profile, $path );
		$provider = CredentialProvider::memoize ( $provider );
			// Instantiate an Amazon S3 client.
		$ses = SesClient::factory ( array (
				'version' => 'latest',
				'region' => 'us-west-2',
				'credentials' => $provider 
		) );
		$request = array ();
		$request ['Source'] = "suresh.krishnan@rootsbridge.com";
		$request ['Destination'] ['ToAddresses'] = array (
				"priyanka@rootsbridge.com" 
		);
		$request ['Message'] ['Subject'] ['Data'] = "Nidara";
		$request ['Message'] ['Body'] ['Text'] ['Data'] = "The following account is requested for deactivation<br><br>
					First name: " . $user->first_name . "<br>
					Last name: " . $user->last_name . "<br>
					Email: " . $user->email . "<br>
					Phone: " . $user->mobile . "";
		$request ['Message'] ['Body'] ['Text'] ['Charset'] = 'UTF-8';
		$request ['Message'] ['Body'] ['Html'] ['Data'] = "The following account is requested for deactivation<br><br>
					First name: " . $user->first_name . "<br>
					Last name: " . $user->last_name . "<br>
					Email: " . $user->email . "<br>
					Phone: " . $user->mobile . "";
		$request ['Message'] ['Body'] ['Html'] ['Charset'] = 'UTF-8';
		$result = $ses->sendEmail($request);
		if ($result) {
			return $this->response->setJsonContent([
					'status' => true,
					'message' => 'Your account is requested for deactivation'
			]);
		} else {
			return $this->response->setJsonContent ( [
					'status' => false,
					'message' => 'Cannot send the mail'
			] );
		
		}
	}
	public function reactivate() {
		try {
			$input_data = $this->request->getJsonRawBody ();
			$headers = $this->request->getHeaders ();
			if (empty ( $headers ['Token'] )) {
				return $this->response->setJsonContent ( [ 
						"status" => false,
						"message" => "Please give the token" 
				] );
			}
			if (empty ( $input_data )) {
				return $this->response->setJsonContent ( [ 
						"status" => false,
						"message" => "Please give the input datas" 
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
			
			/**
			 * This object using valitaion
			 */
			$validation = new Validation ();
			$validation->add ( 'status', new PresenceOf ( [ 
					'message' => 'Status is required' 
			] ) );
			$messages = $validation->validate ( $input_data );
			if (count ( $messages )) :
				foreach ( $messages as $message ) :
					$result [] = $message->getMessage ();
				endforeach
				;
				return $this->response->setJsonContent ( $result );
			 else :
				$userinfo = $this->tokenvalidate->getuserinfo ( $headers ['Token'], $baseurl );
			 	if(empty($userinfo)){
			 		return $this->response->setJsonContent ( [
			 				'status' => false,
			 				'message' => 'Invalid User'
			 		] );
			 	}
				$username = $userinfo->user_info->email;
				$user = Users::findFirstByemail ( $username );
				$user->status = $input_data->status;
				if ($user->save ()) :
					return $this->response->setJsonContent ( [ 
							'status' => true,
							'message' => 'User activated successfully' 
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
					'message' => 'Cannot add kid' 
			] );
		}
	}
	
	/**
	 * This function using to AccountStatus information edit
	 */
	public function update($id = null) {
		$input_data = $this->request->getJsonRawBody ();
		if (empty ( $input_data )) {
			return $this->response->setJsonContent ( [ 
					"status" => false,
					"message" => "Please give the input datas" 
			] );
		}
		
		$id = isset ( $input_data->id ) ? $input_data->id : '';
		if (empty ( $id )) :
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Id is null' 
			] );
		 else :
			$validation = new Validation ();
			
			$validation->add ( 'elaboration', new PresenceOf ( [ 
					'message' => 'elaboration is required' 
			] ) );
			$validation->add ( 'users_id', new PresenceOf ( [ 
					'message' => 'users id is required' 
			] ) );
			$validation->add ( 'why_are_you_leaving_id', new PresenceOf ( [ 
					'message' => 'why are you leaving id is required' 
			] ) );
			$messages = $validation->validate ( $input_data );
			if (count ( $messages )) :
				foreach ( $messages as $message ) :
					$result [] = $message->getMessage ();
				endforeach
				;
				return $this->response->setJsonContent ( $result );
			 else :
				$account = AccountStatus::findFirstByid ( $id );
				if ($account) :
					$account->id = $input_data->id;
					$account->elaboration = $input_data->elaboration;
					$account->users_id = $input_data->users_id;
					$account->why_are_you_leaving_id = $input_data->why_are_you_leaving_id;
					if ($account->save ()) :
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
	}
	/**
	 * This function using delete kids caregiver information
	 */
	public function delete() {
		$input_data = $this->request->getJsonRawBody ();
		$id = isset ( $input_data->id ) ? $input_data->id : '';
		if (empty ( $id )) :
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Id is null' 
			] );
		 else :
			$account = AccountStatus::findFirstByid ( $id );
			if ($account) :
				if ($account->delete ()) :
					return $this->response->setJsonContent ( [ 
							'status' => 'OK',
							'Message' => 'Record has been deleted         successfully ' 
					] );
				 else :
					return $this->response->setJsonContent ( [ 
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
	}
}
