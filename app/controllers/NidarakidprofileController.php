<?php
use Phalcon\Mvc\Micro;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Digit;
use Phalcon\Validation\Validator\Alpha;
use Phalcon\Validation\Validator\Date;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\PresenceOf;
use Aws\Credentials\CredentialProvider;
use Aws\Ses\SesClient;
use Aws\Ses\Exception\SesException;
require BASE_PATH.'/vendor/autoload.php';
class NidarakidprofileController extends \Phalcon\Mvc\Controller {
	public function index() {
	}
	
	/**
	 * Fetch all Record from database :-
	 */
	public function viewall() {
		$subject = NidaraKidProfile::find ();
		if ($subject) :
			return $this->response->setJsonContent ( [ 
					'status' => 'true',
					'data' => $subject 
			] );
		 else :
			return $this->response->setJsonContent ( [ 
					'status' => 'false',
					'Message' => 'Faield' 
			] );
		endif;
	}
	
	/*
	 * Fetch Record from database based on ID :-
	 */
	public function getbyid() {
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
		$id = isset ( $input_data->nidara_kid_profile_id ) ? $input_data->nidara_kid_profile_id : '';
		if (empty ( $id )) :
			return $this->response->setJsonContent ([ 
					'status' => false,
					'message' => 'Invalid input parameter' 
			] );
		 else :
			$kidprofile = NidaraKidProfile::findFirstByid ( $id )->toArray();
			if ($kidprofile) :
				$guided_learning = KidGuidedLearningMap::findFirstBynidara_kid_profile_id ($id);
				if (! empty ( $guided_learning )) {
					$kidprofile['guided_learning_id'] = $guided_learning->id;
				}
				

				return $this->response->setJsonContent ( [ 
						'status' => true,
						'data' =>$kidprofile
				] );
			 else :
				return $this->response->setJsonContent ( [ 
						'status' => false,
						'message' => 'You have not entered any information' 
				] );
			endif;
		endif;
	}
	
	/**
	 * This function using to create NidaraKidProfile information
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
			$user_info=$this->tokenvalidate->getuserinfo ( $headers['Token'], $baseurl );
			$userid = isset ( $user_info->user_info->id ) ? $user_info->user_info->id : '';
		$input_data = $this->request->getJsonRawBody ();
		$id = isset ( $input_data->nidara_kid_profile_id ) ? $input_data->nidara_kid_profile_id : '';
		if (empty ( $input_data )) {
			return $this->response->setJsonContent ( [ 
					"status" => false,
					"message" => "Please give the input datas" 
			] );
		}
		/**
		 * This object using valitaion
		 */
		$validation = new Validation ();
		$validation->add ( 'first_name', new PresenceOf ( [ 
				'message' => 'First Name is required' 
		] ) );
		$validation->add ( 'first_name', new Alpha ( [ 
				'message' => 'First name must contain only letters' 
		] ) );
		$validation->add ( 'first_name', new StringLength ( [ 
				'max' => 20,
				'min' => 2,
				'messageMaximum' => 'First Name is maximum 20',
				'messageMinimum' => 'First Name is minimum 2' 
		] ) );
		if (! empty ( $input_data->middle_name )) {
				$validation->add ( 'middle_name', new Alpha ( [ 
				'message' => 'Middle name must contain only letters' 
		] ) );
		}
		$validation->add ( 'last_name', new PresenceOf ( [ 
				'message' => 'Last Name is required' 
		] ) );
		$validation->add ( 'last_name', new Alpha ( [ 
				'message' => 'Last name must contain only letters' 
		] ) );
		$validation->add ( 'last_name', new StringLength ( [ 
				'max' => 20,
				'min' => 2,
				'messageMaximum' => 'Last Name is maximum 20',
				'messageMinimum' => 'Last Name is minimum 2' 
		] ) );
		$validation->add ( 'date_of_birth', new PresenceOf ( [ 
				'message' => 'Date of birth is required' 
		] ) );
		$validation->add ( 'date_of_birth', new Date ( [ 
				'format' => 'Y-m-d',
				'message' => 'The date is invalid' 
		] ) );
		
		if(!empty($input_data->age)){
		$validation->add ( 'age', new Digit ( [ 
				'message' => 'Please enter a valid Age' 
		] ) );
		}
		$validation->add ( 'gender', new PresenceOf ( [ 
				'message' => 'Gender is required' 
		] ) );
		$validation->add ( 'grade', new PresenceOf ( [ 
				'message' => 'Grade is required' 
		] ) );
		
		$genders = array (
					"male",
					"female" 
			);
		if (! in_array ( $input_data->gender, $genders )) {
				return $this->response->setJsonContent ( [ 
						'status' => false,
						'message' => 'Please give the valid gender' 
				] );
		}
		$messages = $validation->validate ( $input_data );
		if (count ( $messages )) :
			foreach ( $messages as $message ) :
				return $this->response->setJsonContent ( [ 
						'status' => false,
						'message' => $message->getMessage ()
				] );
			endforeach
			;
		 else :
			if (! empty ( $input_data->parent_mobile )) {
					$mobile = Users::findFirstBymobile ( $input_data->parent_mobile );
					if (empty ( $mobile )) {
						return $this->response->setJsonContent ( [ 
								'status' => false,
								'message' => 'Please enter valid parent mobile number to add the kid information' 
						] );
					}
				}
			if (! empty ( $input_data->board_of_education )) {
					$board = BoardOfEducation::findFirstByid ( $input_data->board_of_education );
					if (empty ( $board )) {
						return $this->response->setJsonContent ( [ 
								'status' => false,
								'message' => 'Board of education is invalid' 
						] );
					}
				}
				if (! empty ( $input_data->relationship_to_child )) {
					$relationship = Relationships::findFirstByid ( $input_data->relationship_to_child );
					if (empty ( $relationship )) {
						return $this->response->setJsonContent ( [ 
								'status' => false,
								'message' => 'Relationship is invalid' 
						] );
					}
				}
				if (! empty ( $input_data->grade )) {
					$grade = Grade::findFirstByid ( $input_data->grade );
					if (empty ( $grade )) {
						return $this->response->setJsonContent ( [
								'status' => false,
								'message' => 'Grade is invalid'
						] );
					}
				}
			$kidprofile = NidaraKidProfile::findFirstByid ( $id );
			if (empty($kidprofile)) {
				$kidprofile = new NidaraKidProfile ();
				$kidprofile->id = $this->kididgen->getNewId ( "nidarakidprofile" );
			}
			$kidprofile->first_name = $input_data->first_name;
			if (! empty ( $input_data->middle_name )) {
				$kidprofile->middle_name = $input_data->middle_name;
			}
			$kidprofile->last_name = $input_data->last_name;
			$kidprofile->date_of_birth = $input_data->date_of_birth;
			if(!empty($input_data->age)){
			$kidprofile->age = $input_data->age;
			}
			$kidprofile->gender = $input_data->gender;
			$kidprofile->grade = $input_data->grade;
			$kidprofile->child_photo = $input_data->child_photo;
			// $kidprofile_create->child_avatar = $input_data->child_avatar;
			$kidprofile->created_at = date ( 'Y-m-d H:i:s' );
			$kidprofile->created_by = $userid;
			$kidprofile->modified_at = date ( 'Y-m-d H:i:s' );
			$kidprofile->status = 1;
			$kidprofile->cancel_subscription = 1;
			if(!empty($input_data->board_of_education)){
			$kidprofile->board_of_education = $input_data->board_of_education;
			}
			if(!empty($input_data->relationship_to_child)){
			$kidprofile->relationship_to_child = $input_data->relationship_to_child;
		        }
			if (empty ( $input_data->child_photo )) {
				$gender = $input_data->gender;
				if ($gender == 'male') {
					$kidprofile->child_photo = 'https://s3.amazonaws.com/nidara-dev/dev-files/no_image_male.png';
				} else {
					$kidprofile->child_photo = 'https://s3.amazonaws.com/nidara-dev/dev-files/no_image_female.png';
				}
			} else {
				$kidprofile->child_photo = $input_data->child_photo;
			}
			if ($kidprofile->save ()) {
				if (! empty ( $input_data->guided_learning_id )) {
					$kid_guide=KidGuidedLearningMap::findFirstBynidara_kid_profile_id($kidprofile->id);
					if(empty($kid_guide)){
					$kid_guide = new KidGuidedLearningMap ();
					$kid_guide->id = $this->kididgen->getNewId ( "nidarakidlearningmap" );
					$kid_guide->nidara_kid_profile_id = $kidprofile->id;
					$kid_guide->guided_learning_id = $input_data->guided_learning_id;
					$kid_guide->save ();
					}
						
				}
					$parentsmap=KidParentsMap::findFirstBynidara_kid_profile_id($kidprofile->id);
					if(empty($parentsmap)){
						$parentsmap = new KidParentsMap ();
						$parentsmap->id = $this->kididgen->getNewId ( "kidparentsmap" );
						$parentsmap->nidara_kid_profile_id = $kidprofile->id;
						$parentsmap->users_id = $userid;
						$parentsmap->save();
					}
				return $this->response->setJsonContent ([ 
						'status' => true,
						'message' => 'Child details saved successfully',
						'kid_id' => $kidprofile->id 
				]);
			} else {
					return $this->response->setJsonContent ( [ 
							'status' => false,
							'message' => 'Cannot add kid' 
					] );
				}
		

		endif;
	}
	
	/**
	 * This function using to NidaraKidProfile information edit
	 */
	public function update() {
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
		$id = isset ( $input_data->nidara_kid_profile_id ) ? $input_data->nidara_kid_profile_id : '';
		if (empty ( $id )) {
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Nidara kid profile id is empty' 
			] );
		}
		$validation = new Validation ();
		$validation->add ( 'first_name', new PresenceOf ( [ 
				'message' => 'Frirst Name is required' 
		] ) );
		$validation->add ( 'first_name', new Alpha ( [ 
				'message' => 'Field must contain only letters' 
		] ) );
		$validation->add ( 'first_name', new StringLength ( [ 
				'max' => 20,
				'min' => 2,
				'messageMaximum' => 'Frirst Name is maximum 20',
				'messageMinimum' => 'Frirst Name is minimum 2' 
		] ) );
		if (! empty ( $input_data->middle_name )) {
				$validation->add ( 'middle_name', new Alpha ( [ 
				'message' => 'Middle name must contain only letters' 
		] ) );
		}
		$validation->add ( 'last_name', new PresenceOf ( [ 
				'message' => 'Last Name is required' 
		] ) );
		$validation->add ( 'last_name', new Alpha ( [ 
				'message' => 'Field must contain only letters' 
		] ) );
		$validation->add ( 'last_name', new StringLength ( [ 
				'max' => 20,
				'min' => 2,
				'messageMaximum' => 'Last Name is maximum 20',
				'messageMinimum' => 'Last Name is minimum 2' 
		] ) );
		$validation->add ( 'date_of_birth', new PresenceOf ( [ 
				'message' => 'Date of birth is required' 
		] ) );
		$validation->add ( 'date_of_birth', new Date ( [ 
				'format' => 'Y-m-d',
				'message' => 'The date is invalid' 
		] ) );
		$validation->add ( 'age', new PresenceOf ( [ 
				'message' => 'Age is required' 
		] ) );
		$validation->add ( 'age', new Digit ( [ 
				'message' => 'Age is only digit' 
		] ) );
		$validation->add ( 'gender', new PresenceOf ( [ 
				'message' => 'Gender is required' 
		] ) );
		$validation->add ( 'grade', new PresenceOf ( [ 
				'message' => 'Grade is required' 
		] ) );
		$messages = $validation->validate ( $input_data );
		if (count ( $messages )) {
			foreach ( $messages as $message ) {
				$result [] = $message->getMessage ();
			}
			return $this->response->setJsonContent ( $result );
		}
		$kidprofile = NidaraKidProfile::findFirstByid ( $id );
		if ($kidprofile) {
			$kidprofile->first_name = $input_data->first_name;
			if (! empty ( $input_data->middle_name )) {
				$kidprofile->middle_name = $input_data->middle_name;
			}
			$kidprofile->last_name = $input_data->last_name;
			$kidprofile->date_of_birth = $input_data->date_of_birth;
			if(!empty($input_data->age)){
			$kidprofile->age = $input_data->age;
			}
			$kidprofile->gender = $input_data->gender;
			$kidprofile->grade = $input_data->grade;
			$kidprofile->modified_at = date ( 'Y-m-d H:i:s' );
			if (empty ( $input_data->child_photo )) {
				$gender = $input_data->gender;
				if ($gender == 'male') {
					$kidprofile->child_photo = "https://s3.amazonaws.com/nidara-dev/dev-files/no_image_male.png";
				} else {
					$kidprofile->child_photo = "https://s3.amazonaws.com/nidara-dev/dev-files/no_image_female.png";
				}
			} else {
				$kidprofile->child_photo = $input_data->child_photo;
			}
			if ($kidprofile->save ()) {
				return $this->response->setJsonContent ([ 
						'status' => true,
						'message' => 'Child details updated successfully' 
				]);
			} else {
				return $this->response->setJsonContent ( [ 
						'status' => false,
						'message' => 'Failed' 
				] );
			}
		}
	}
	
	/**
	 * This function using delete kids caregiver information
	 */
	public function delete() {
		$input_data = $this->request->getJsonRawBody ();
		$id = isset ( $input_data->id ) ? $input_data->id : '';
		if (empty ( $id )) :
			return $this->response->setJsonContent ( [ 
					'status' => 'Error',
					'message' => 'Id is null' 
			] );
		 else :
			$collection = NidaraKidProfile::findFirstByid ( $id );
			if ($collection) :
				if ($collection->delete ()) :
					return $this->response->setJsonContent ( [ 
							'status' => 'OK',
							'Message' => 'Record has been deleted succefully ' 
					] );
				 else :
					return $this->response->setJsonContent ( [ 
							'status' => 'Error',
							'Message' => 'Data could not be deleted' 
					] );
				endif;
			 else :
				return $this->response->setJsonContent ( [ 
						'status' => 'Error',
						'Message' => 'ID doesn\'t' 
				] );
			endif;
		endif;
	}
	public function kid_board_of_education() {
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
		$id = isset ( $input_data->nidara_kid_profile_id ) ? $input_data->nidara_kid_profile_id : '';
		if (empty ( $id )) {
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Id is null' 
			] );
		} else {
			if (! empty ( $input_data->board_of_education )) {
				$board = BoardOfEducation::findFirstByid ( $input_data->board_of_education );
				if (empty ( $board )) {
					return $this->response->setJsonContent ( [ 
							'status' => false,
							'message' => 'Board of education is invalid' 
					] );
				}
			}else{
				return $this->response->setJsonContent ( [
						'status' => false,
						'message' => 'Board of education is required'
				] );
			}
			if (! empty ( $input_data->grade )) {
				$grade = Grade::findFirstByid ( $input_data->grade );
				if (empty ( $grade )) {
					return $this->response->setJsonContent ( [
							'status' => false,
							'message' => 'Grade is invalid'
					] );
				}
			}else{
				return $this->response->setJsonContent ( [
						'status' => false,
						'message' => 'Grade is required'
				] );
			}
			$kid_board_update = NidaraKidProfile::findFirstByid ( $id );
			if ($kid_board_update) {
				$kid_board_update->board_of_education = $input_data->board_of_education;
				$kid_board_update->grade = $input_data->grade;
				if ($kid_board_update->save ()) {
					return $this->response->setJsonContent ( [ 
							'status' => true,
							'message' => 'Kid profile updated successfully' 
					] );
				} else {
					return $this->response->setJsonContent ( [ 
							'status' => false,
							'message' => 'Failed' 
					] );
				}
			} else {
				return $this->response->setJsonContent ( [ 
						'status' => false,
						'Message' => 'ID doesn\'t' 
				] );
			}
		}
	}
	public function cancel_subscription() {
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
			$id = isset ( $input_data->nidara_kid_profile_id ) ? $input_data->nidara_kid_profile_id : '';
			if (empty ( $id )) {
				return $this->response->setJsonContent ( [ 
						'status' => false,
						'message' => 'Id is null' 
				] );
			} else {
				$user_info=$this->tokenvalidate->getuserinfo ( $headers['Token'], $baseurl );
				$userid = isset ( $user_info->user_info->id ) ? $user_info->user_info->id : '';
				$kid_payment_info = NidaraKidProfile::findFirstByid ( $id );
				if ($kid_payment_info) {
					$parent_id = array ();
					$user_id = array ();
					$kid_payment_info->cancel_subscription = 2;
					if ($kid_payment_info->save ()) {
						$kidparentmap = KidParentsMap::findFirstBynidara_kid_profile_id ( $input_data->kid_id );
						$parent_id = $kidparentmap->users_id;
						$user = Users::findFirstByid ( $userid );
						$profile = 'default';
						$path = APP_PATH . '/library/credentials.ini';
						$provider = CredentialProvider::ini ( $profile, $path );
						$provider = CredentialProvider::memoize ( $provider );
						// Instantiate an Amazon S3 client.
						$ses = SesClient::factory ( array (
								'version' => 'latest',
								'region' => 'us-east-1',
								'credentials' => $provider 
						) );
						
						$request ['Source'] = "priyanka@rootsbridge.com";
						$request ['Destination'] ['ToAddresses'] = array (
								"priyanka@rootsbridge.com" 
						);
						$request ['Message'] ['Subject'] ['Data'] = "Nidara";
						$baseurl = $this->config->appurl;
						$changeurl = $baseurl . '/?token=' . $token;
						$request ['Message'] ['Body'] ['Text'] ['Data'] = "Folowing User is requested for the cancellation<br><br>
					First name: " . $user->first_name . "<br>
					Last name: " . $user->last_name . "<br>
					Email: " . $user->email . "<br>
					Phone: " . $user->mobile . "<br>
					Kid Name:" . $kid_payment_info->first_name . "";
						$request ['Message'] ['Body'] ['Text'] ['Charset'] = 'UTF-8';
						$request ['Message'] ['Body'] ['Html'] ['Data'] = "Folowing User is requested for the cancellation<br><br>
					First name: " . $user->first_name . "<br>
					Last name: " . $user->last_name . "<br>
					Email: " . $user->email . "<br>
					Phone: " . $user->mobile . "<br>
					Kid Name:" . $kid_payment_info->first_name . "";
						$request ['Message'] ['Body'] ['Html'] ['Charset'] = 'UTF-8';
						$result = $ses->sendEmail ( $request );
						if ($result) {
							return $this->response->setJsonContent ( [ 
									'status' => true,
									'message' => 'Your account is requested for cancellation' 
							] );
						} else {
							return $this->response->setJsonContent ( [ 
									'status' => false,
									'message' => 'Cannot send the mail' 
							] );
						}
					} else {
						return $this->response->setJsonContent ( [ 
								'status' => false,
								'message' => 'Failed' 
						] );
					}
				} else {
					return $this->response->setJsonContent ( [ 
							'status' => false,
							'Message' => 'ID doesn\'t' 
					] );
				}
			}
		} catch ( Exception $e ) {
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Cannot cancel subscribe' 
			] );
		}
	}
}
