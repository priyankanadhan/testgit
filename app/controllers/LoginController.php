<?php

use Phalcon\Mvc\Micro;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;
use Aws\Credentials\CredentialProvider;
use Aws\Ses\SesClient;
use Aws\Ses\Exception\SesException;

require BASE_PATH.'/vendor/autoload.php';

class LoginController extends \Phalcon\Mvc\Controller {
	CONST ldaprdn = 'cn=admin,dc=rootsbridge,dc=com';
	CONST ldappass = 'rootsbridge@123';
	CONST host = 'ec2-54-245-128-218.us-west-2.compute.amazonaws.com';
	public function connection() {
		// using ldap bind
		$ldaprdn = 'cn=admin,dc=rootsbridge,dc=com'; // ldap rdn or dn
		$ldappass = 'rootsbridge@123'; // associated password
		$binddn = "ou=rootsbridge-chennai,cn=admin,dc=rootsbridge,dc=com";
		// connect to ldap server
		$ldapconn = ldap_connect ( self::host ) or die ( "Could not connect to LDAP server." );
		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
	    return $ldapconn;
    }
		
	/**
	 * Fetch Record from database based on ID :-
	 */
	public function getbyid($id = null) {
		$input_data = $this->request->getJsonRawBody ();
		$id = isset ( $input_data->id ) ? $input_data->id : '';
		if (empty ( $id )) :
			return $this->response->setJsonContent ( [ 
					'status' => 'Error',
					'message' => 'Invalid input parameter' 
			] );
		 else :
			$collection = Subject::findFirstByid ( $id );
			if ($collection) :
				return Json_encode ( $collection );
			 else :
				return $this->response->setJsonContent ( [ 
						'status' => 'Error',
						'Message' => 'Data not found' 
				] );
			endif;
		endif;
	}
	
	/**
	 * This function using to register the user
	 */
	public function register() {
		$input_data = $this->request->getJsonRawBody ();
		if(empty($input_data)){
			return $this->response->setJsonContent([
					'status' => false,
					'message' => "Please give the details and then login"
			] );
		}
		$validation = new Validation ();
		$validation->add ( 'first_name', new PresenceOf ( [ 
				'message' => 'First name is required' 
		] ) );
		$validation->add ( 'last_name', new PresenceOf ( [ 
				'message' => 'Last name is required' 
		] ) );
		$validation->add ( 'email', new PresenceOf ( [
				'message' => 'Email is required'
		] ) );
		$validation->add ( 'email', new Email ( [
				'message' => 'Please give the valid email'
		] ) );
		$validation->add ( 'password', new PresenceOf ( [ 
				'message' => 'Password is required' 
		] ) );
		$validation->add ( 'confirmpassword', new PresenceOf ( [
				'message' => 'Confirm Password is required'
		] ) );
		$validation->add ( 'parent_type', new PresenceOf ( [
				'message' => 'Parent Type is required'
		] ) );
		$messages = $validation->validate ( $input_data );
		if (count ( $messages )) {
			
			foreach ( $messages as $message ) {
				$result [] = $message->getMessage ();
			}
			return $this->response->setJsonContent([ 
					'status' => false,
					'message' => $result
			] );
		}
		if ($input_data->password != $input_data->confirmpassword) {
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => "Password doesnot match" 
			] );
		}
		$ldapconn = $this->connection ();
		$email = $input_data->email;
		$user_id = $this->usercreate ( $input_data );
		$ldapbind = ldap_bind($ldapconn, self::ldaprdn, self::ldappass);
		if ($ldapbind) {
			/**
			 * This object using valitaion
			 */
			$ldaprecord ['sn'] [0] = $input_data->first_name;
			$ldaprecord ['objectclass'] [2] = "top";
			$ldaprecord ['objectclass'] [1] = "posixAccount";
			$ldaprecord ['objectclass'] [0] = "inetOrgPerson";
			$ldaprecord ['uid'] [0] = $email;
			$ldaprecord ['gidnumber'] [0] = '500';
			$ldaprecord ['givenname'] [0] = $input_data->first_name;
			$ldaprecord ['uidnumber'] [0] = $user_id;
			$ldaprecord ['userpassword'] [0] = md5 ( $input_data->password );
			$ldaprecord ['loginshell'] [0] = '/bin/sh';
			$ldaprecord ['homedirectory'] [0] = "/home/users/$email";
			$ldaprecord ['street'] [0] = $email;
			
			// add data to directory
			$r = ldap_add ( $ldapconn, 'cn=' . $email . ',ou=users,' . self::ldaprdn, $ldaprecord );
			if ($r) {
				return $this->response->setJsonContent ( [ 
						'status' => true,
						'message' => 'User registered successfully' 
				] );
			} else {
				return $this->response->setJsonContent ( [ 
						'status' => false,
						'message' => 'couldnot save user' 
				] );
			}
		} else {
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'LDAP bind failed' 
			] );
		}
	}
	
	/**
	 * Create user
	 * @param object $input_data
	 */
	function usercreate($input_data) {
		$user = new Users ();
		$user->id = $this->usersid->getNewId("users");
		$user->parent_type = $input_data->parent_type;
		$user->first_name = $input_data->first_name;
		$user->last_name = $input_data->last_name;
		$user->user_type = 'parent';
		$user->email = $input_data->email;
		$user->mobile = $input_data->mobile;
		$user->created_at = date ( 'Y-m-d H:i:s' );
		$user->created_by = 1;
		$user->modified_at = date ( 'Y-m-d H:i:s' );
		$user->save ();
		$parents_map = new ParentsMappingProfiles ();
		$parents_map->id = $this->parentsidgen->getNewId("parentsmap");
		$parents_map->primary_parents_id = $user->id;
		$parents_map->primary_parent_type = $input_data->parent_type;
		$parents_map->save ();
		return $user->id;
	}
	
	/**
	 * This function using to Subject information edit
	 */
	public function forgotpassword() {
		$ldapconn = $this->connection ();
		$input_data = $this->request->getJsonRawBody ();
		if(empty($input_data->username)){
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Please give the username' 
			] );
		}
		// Forget password
		$email = array (
				"street" 
		);
		// Search surname entry
		$user = ldap_search ( $ldapconn, self::ldaprdn, "uid=stest", $email );
		$ses = $this->sesConfiguration ();
		$userinfo = ldap_get_entries ( $ldapconn, $user );
		$authentication = new AuthenticationController ();
		$token = ( string ) $authentication->tokengenerate ( $userinfo [0] ['uidnumber'] [0],$input_data->username );
		$request = array ();
		$request ['Source'] = "suresh.krishnan@rootsbridge.com";
		$request ['Destination'] ['ToAddresses'] = array (
				"priyanka@rootsbridge.com" 
		);
		$request ['Message'] ['Subject'] ['Data'] = "Nidara";
		$baseurl = $this->config->appurl;
		$changeurl = $baseurl . '/?token=' . $token;
		$request ['Message'] ['Body'] ['Text'] ['Data'] = "Please click the below link to change your password<br><br>
						<u><a href=".$changeurl.">".$baseurl."</a></u>";
		$request ['Message'] ['Body'] ['Text'] ['Charset'] = 'UTF-8';
		$request ['Message'] ['Body'] ['Html'] ['Data'] = "Please click the below link to change your password<br><br>
					    <u><a href=".$changeurl.">".$baseurl."</a></u>";
		$request ['Message'] ['Body'] ['Html'] ['Charset'] = 'UTF-8';
		try {
		$result = $ses->sendEmail($request);
		if ($result) {
				return $this->response->setJsonContent ( [ 
						'status' => true,
						'message' => 'Please click the link in your email to update your password' 
				] );
			} else {
				return $this->response->setJsonContent ( [
						'status' => false,
						'message' => 'Cannot send the mail'
				] );
				
			}
		} catch (SesException $e) {
			return $this->response->setJsonContent ( [
					'status' => false,
					'message' => 'The email was not sent.'
			] );
		}
	}
	
	/**
	 * Change password
	 */
	function changepassword(){
		$input_data = $this->request->getJsonRawBody ();
		$headers = $this->request->getHeaders ();
		if (empty ( $headers ['Token'] )) {
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Please give the token' 
			] );
		}
		$authentication = new AuthenticationController ();
		$validatetoken = $authentication->validatetoken ( $headers ['Token'] );
		$userdetail = $authentication->getuidtoken ( $headers ['Token'] );
		if (empty($validatetoken)) {
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Invalid User' 
			] );
		}
		if(empty($input_data)){
			return $this->response->setJsonContent([
					'status' => false,
					'message' => "Please fill the form"
			] );
		}
		$validation = new Validation ();
		$validation->add ( 'oldpassword', new PresenceOf ( [
				'message' => 'Old password is required'
		] ) );
		$validation->add ( 'password', new PresenceOf ( [
				'message' => 'Password is required'
		] ) );
		$validation->add ( 'confirmpassword', new PresenceOf ( [
				'message' => 'Confirm Password is required'
		] ) );
		$messages = $validation->validate ( $input_data );
		if (count ( $messages )) {
				
			foreach ( $messages as $message ) {
				$result [] = $message->getMessage ();
			}
			return $this->response->setJsonContent([
					'status' => false,
					'message' => $result
			] );
		}
		if ($input_data->password != $input_data->confirmpassword) {
			return $this->response->setJsonContent ( [
					'status' => false,
					'message' => "Password doesnot match"
			] );
		}
		$ldapconn = $this->connection ();
		$ldapbind = ldap_bind($ldapconn, self::ldaprdn, self::ldappass);
		if ($ldapbind) {
			$search = "uid=" . $userdetail['username'];
			$user = ldap_search ( $ldapconn, self::ldaprdn, $search );
			$userinfo = ldap_get_entries ( $ldapconn, $user );
			if (! empty ( $userinfo ['count'] )) {
				$ldapBindUser = ldap_bind ( $ldapconn, $userinfo [0] ['dn'], md5 ( $input_data->oldpassword ) );
				if (empty($ldapBindUser)) {
					return $this->response->setJsonContent ( [ 
							'status' => false,
							'message' => 'Invalid old password' 
					] );
				}
				$userdata ['userpassword'] = md5 ( $input_data->password );
				$result = ldap_mod_replace ( $ldapconn, 'cn=' . $userdetail['username']. ',ou=users,' . self::ldaprdn, $userdata );
				if ($result) {
					return $this->response->setJsonContent ( [ 
							'status' => true,
							'message' => 'Password changed successfully' 
					] );
				} else {
					return $this->response->setJsonContent ( [ 
							'status' => false,
							'message' => 'Couldnot change the password' 
					] );
				}
			} else {
				return $this->response->setJsonContent ( [ 
						'status' => false,
						'message' => 'Invalid username or password' 
				] );
			}
		} else {
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'LDAP bind failed' 
			] );
		}
	}
	/**
	 * Check the login credential and create token from the JWT
	 */
	public function logincheck() {
		$ldapconn = $this->connection ();
		$input_data = $this->request->getJsonRawBody ();
		if(empty($input_data)){
			return $this->response->setJsonContent([
					'status' => false,
					'message' => "Please give the details and then login"
			]);
		}
		$validation = new Validation ();
		$validation->add ( 'username', new PresenceOf ( [ 
				'message' => 'Username is required' 
		] ) );
		$validation->add ( 'password', new PresenceOf ( [ 
				'message' => 'Password is required' 
		] ) );
		$messages = $validation->validate ( $input_data );
		if (count ( $messages )) {
			
			foreach ( $messages as $message ) {
				$result [] = $message->getMessage ();
			}
			return $this->response->setJsonContent([ 
					'status' => false,
					'message' => $result
			] );
		}
		$password = md5 ( $input_data->password );
		$search = "uid=" . $input_data->username;
		$user = ldap_search ( $ldapconn, self::ldaprdn, $search );
		$userinfo = ldap_get_entries ( $ldapconn, $user );
		if (! empty ( $userinfo ['count'] )) {
			$ldapBindUser = ldap_bind ( $ldapconn, $userinfo [0] ['dn'], md5 ( $input_data->password ) );
			if ($ldapBindUser) {
				$authentication = new AuthenticationController ();
				$token = ( string ) $authentication->tokengenerate ( $userinfo [0] ['uidnumber'] [0], $input_data->username );
				//$this->tokenadd ( $token, $userinfo [0] ['uidnumber'] [0] );
				$user = Users::findFirstByid ( $userinfo [0] ['uidnumber'] [0]);
				return $this->response->setJsonContent ( [
						'status' => true,
						'token' => $token,
						'is_active'=> $user->status,
						'message' => 'Login successfully'
				] );
			} else {
				return $this->response->setJsonContent ( [ 
						'status' => false,
						'message' => 'Invalid username or password' 
				] );
			}
		} else {
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Invalid username and password' 
			] );
		}
	}
	/**
	 * Add token info
	 * 
	 * @param string $token        	
	 * @param integer $users_id        	
	 */
	public function tokenadd($token, $users_id) {
		$token_users = TokenUsers::findFirstByusers_id ( $users_id );
		if (empty ( $token_users )) {
			$token_data = new TokenUsers ();
			$token_data->id = $this->usersid->getNewId ( "token" );
		}
		$token_data->token = $token;
		$token_data->users_id = $users_id;
		$token_data->save ();
	}
	/**
	 * Check token
	 * @param token
	 * @return array
	 */
	function tokencheck() {
		$input_data = $this->request->getJsonRawBody ();
		$headers = $this->request->getHeaders ();
		if (empty ( $headers ['Token'] )) {
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Please give the token' 
			] );
		}
		$authentication = new AuthenticationController ();
		$validatetoken = $authentication->validatetoken ( $headers ['Token'] );
		$token_users = TokenUsers::findFirstBytoken ( $headers ['Token'] );
		if (empty ( $validatetoken )) {
			$useractive = $this->useractive ( $headers ['Token'] );
			if (empty ( $useractive->status )) {
				return $this->response->setJsonContent ( [ 
						'status' => false,
						'message' => 'Invalid user' 
				] );
			} else {
				return $this->response->setJsonContent ( [ 
						'status' => true,
						'message' => 'Valid User',
						"refresh_token" => $tokennew 
				] );
			}
			if (! empty ( $token_users )) {
				$token_users->delete ();
			}
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Invalid user' 
			] );
		}
		if (! empty ( $token_users )) {
			$token_users->last_modified_at = date ( "Y-m-d H:i:s" );
			$token_users->save ();
		}
		if ($validatetoken && ! empty ( $token_users )) {
			return $this->response->setJsonContent ( [ 
					'status' => true,
					'message' => 'Valid User' 
			] );
		} else {
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Invalid User' 
			] );
		}
	}
	public function useractive($token){
		$token_users = TokenUsers::findFirstBytoken ( $token );
		$timestamp = strtotime ( $token_users->last_modified_at );
		if ($timestamp > strtotime ( "-60 minutes" )) {
			return array (
					"success" => false
			);
		} else {
			$authentication = new AuthenticationController ();
			$tokennew = ( string ) $authentication->tokengenerate ( $userinfo [0] ['uidnumber'] [0], $input_data->username );
			$token_users->token = $tokennew;
			$token_users->last_modified_at = date ( "Y-m-d H:i:s" );
			$token_users->save ();
			return array (
					"success" => true,
					"refresh_token" => $tokennew 
			);
		}
	}
	public function sesConfiguration(){
		$profile = 'default';
		$path = APP_PATH . '/library/credentials_mail.ini';
		
		$provider = CredentialProvider::ini ( $profile, $path );
		$provider = CredentialProvider::memoize ( $provider );
		// Instantiate an Amazon S3 client.
		$ses = SesClient::factory(array(
				'version' => 'latest',
				'region'  => 'us-west-2',
				'credentials' => $provider
		));
		return $ses;
	}
	
	/**
	 * Get profile info by token
	 */
	public function getuserinfobytoken() {
		$headers = $this->request->getHeaders ();
		if (empty ( $headers ['Token'] )) {
			return $this->response->setJsonContent ( [
					'status' => false,
					'message' => 'Please give the token'
			] );
		}
		$authentication = new AuthenticationController ();
		$user = $authentication->getuidtoken ( $headers ['Token'] );
		$userdata = Users::findFirst ( $user ['uid'] )->toArray();
		if ($userdata) {
			return $this->response->setJsonContent ( [
					'status' => true,
					'user_info' => $userdata
			] );
		} else {
			return $this->response->setJsonContent ( [
					'status' => false,
					'message' => 'Invalid User'
			] );
		}
	}

	/**
	 * Check the password is to get into the parent dashboard
	 */
public function parentvalidate() {
		$ldapconn = $this->connection ();
		$input_data = $this->request->getJsonRawBody ();
		$headers = $this->request->getHeaders ();
		if (empty ( $headers ['Token'] )) {
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Please give the token' 
			] );
		}
		if (empty ( $input_data->password )) {
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Please give the password' 
			] );
		}
		$authentication = new AuthenticationController ();
		$tokencheck=$authentication->validatetoken( $headers ['Token'] );
		if(empty($tokencheck)){
		$user = $authentication->getuidtoken ( $headers ['Token'] );
		return $this->response->setJsonContent ( [ 
						'status' => false,
						'message' => 'Invalid user',
						'username'=> $user ['username'] 
				] );
		}
		$search = "uid=" . $user['username'];
		$usersearch = ldap_search ( $ldapconn, self::ldaprdn, $search );
		$userinfo = ldap_get_entries ( $ldapconn, $usersearch );
		if (! empty ( $userinfo ['count'] )) {
			$ldapBindUser = ldap_bind ( $ldapconn, $userinfo [0] ['dn'], md5 ( $input_data->password ) );
			if ($ldapBindUser) {
				return $this->response->setJsonContent ( [ 
						'status' => true,
						'message' => 'Valid User',
						'username'=> $user ['username'] 
				] );
			} else {
				return $this->response->setJsonContent ( [ 
						'status' => false,
						'message' => 'Invalid username or password' 
				] );
			}
		} else {
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Invalid username and password' 
			] );
		}
	}
	

     	/**
	 * Reset password
	 */
	public function resetpassword() {
		$input_data = $this->request->getJsonRawBody ();
		$headers = $this->request->getHeaders ();
		if (empty ( $headers ['Token'] )) {
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => "Please give the token" 
			] );
		}
		$authentication = new AuthenticationController ();
		$validatetoken = $authentication->validatetoken ( $headers ['Token'] );
		$userdetail = $authentication->getuidtoken ( $headers ['Token'] );
		if (empty ( $validatetoken )) {
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Invalid User' 
			] );
		}
		if (empty ( $input_data )) {
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => "Please fill the form" 
			] );
		}
		$validation = new Validation ();
		$validation->add ( 'password', new PresenceOf ( [ 
				'message' => 'Password is required' 
		] ) );
		$validation->add ( 'confirmpassword', new PresenceOf ( [ 
				'message' => 'Confirm Password is required' 
		] ) );
		$messages = $validation->validate ( $input_data );
		if (count ( $messages )) {
			
			foreach ( $messages as $message ) {
				$result [] = $message->getMessage ();
			}
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => $result 
			] );
		}
		if ($input_data->password != $input_data->confirmpassword) {
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => "Password doesnot match" 
			] );
		}
		$ldapconn = $this->connection ();
		$ldapbind = ldap_bind ( $ldapconn, self::ldaprdn, self::ldappass );
		if ($ldapbind) {
			$search = "uid=" . $userdetail['username'];
			$user = ldap_search ( $ldapconn, self::ldaprdn, $search );
			$userinfo = ldap_get_entries ( $ldapconn, $user );
			if (empty ( $userinfo ['count'] )) {
				return $this->response->setJsonContent ([ 
						'status' => false,
						'message' => 'Invalid Username' 
				]);
			}
			$userdata ['userpassword'] = md5 ( $input_data->password );
			$result = ldap_mod_replace ( $ldapconn, 'cn=' . $userdetail['username'] . ',ou=users,' . self::ldaprdn, $userdata );
			if ($result) {
				$tokenexpire = $authentication->validatetoken ( $headers ['Token'],"logout" );
				return $this->response->setJsonContent ( [ 
						'status' => true,
						'message' => 'Password changed successfully' 
				] );
			} else {
				return $this->response->setJsonContent ( [ 
						'status' => false,
						'message' => 'Couldnot change the password' 
				] );
			}
		} else {
			return $this->response->setJsonContent ([ 
					'status' => false,
					'message' => 'LDAP bind failed' 
			]);
		}
	}

	/**
	 * Logout
	 */
	public function logout() {
		$headers = $this->request->getHeaders ();
		if (empty ( $headers ['Token'] )) {
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Please give the token' 
			] );
		}
		$token = TokenUsers::findFirstBytoken($headers ['Token']);
		$authentication = new AuthenticationController ();
		$expiretoken = $authentication->validatetoken ( $headers ['Token'],'logout' );	
		if ($token->delete ()) {
			return $this->response->setJsonContent ([ 
					'status' => true,
					'message' => 'User logout successfully' 
			]);
		} else {
			return $this->response->setJsonContent ([ 
					'status' => false,
					'message' => 'Cannot logout' 
			]);
		}
	}
}
