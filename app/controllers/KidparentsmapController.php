<?php
use Phalcon\Mvc\Micro;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
class KidparentsmapController extends \Phalcon\Mvc\Controller {
	public function index() {
	}
	/**
	 * Fetch all Record from database :-
	 */
	public function viewall() {
		$kidparentsmap = KidParentsMap::find ();
		if ($kidparentsmap) :
			
			return $this->response->setJsonContent ( [ 
					'status' => true,
					'data' =>$kidparentsmap 
			] );
			
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
	public function getbyid() {
		$input_data = $this->request->getJsonRawBody ();
		$id = isset ( $input_data->id ) ? $input_data->id : '';
		if (empty ( $id )) :
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Invalid input parameter' 
			] );
		 else :
			$kidparentsmap_getbyid = KidParentsMap::findFirstByid ( $id );
			if ($kidparentsmap_getbyid) :

				return $this->response->setJsonContent ( [ 
					'status' => true,
					'data' =>$kidparentsmap_getbyid 
			] );
				
			 else :
				return $this->response->setJsonContent ( [ 
						'status' => false,
						'Message' => 'Data not found' 
				] );
			endif;
		endif;
	}
	/**
	 * This function using to create KidParentsMap information
	 */
	public function create() {
		$input_data = $this->request->getJsonRawBody ();
		
		/**
		 * This object using valitaion
		 */
		$validation->add ( 'nidara_kid_profile_id', new PresenceOf ( [ 
				'message' => 'nidara_kid_profile_id is required' 
		] ) );
		$validation->add ( 'users_id', new PresenceOf ( [ 
				'message' => 'users_id is required' 
		] ) );
		$messages = $validation->validate ( $input_data );
		if (count ( $messages )) :
			foreach ( $messages as $message ) :
				$result [] = $message->getMessage ();
			endforeach
			;
			return $this->response->setJsonContent ( $result );
		 else :
			$collection = new KidParentsMap ();
			$collection->id = $input_data->id;
			$kidparentsmap_create->nidara_kid_profile_id = $input_data->nidara_kid_profile_id;
			$kidparentsmap_create->users_id = $input_data->users_id;
			if ($kidparentsmap_create->save ()) :
				return $this->response->setJsonContent ( [ 
						'status' => true,
						'message' => 'succefully' 
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
	 * This function using to KidParentsMap information edit
	 */
	public function update() {
		$input_data = $this->request->getJsonRawBody ();
		$id = isset ( $input_data->id ) ? $input_data->id : '';
		if (empty ( $id )) :
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Id is null' 
			] );
		 else :
			$validation = new Validation ();
			
			$validation->add ( 'nidara_kid_profile_id', new PresenceOf ( [ 
					'message' => 'nidara_kid_profile_idis required' 
			] ) );
			$validation->add ( 'users_id', new PresenceOf ( [ 
					'message' => 'users_idis required' 
			] ) );
			$messages = $validation->validate ( $input_data );
			if (count ( $messages )) :
				foreach ( $messages as $message ) :
					$result [] = $message->getMessage ();
				endforeach
				;
				return $this->response->setJsonContent ( $result );
			 else :
				$kidparentsmap_update = KidParentsMap::findFirstByid ( $id );
				if ($kidparentsmap_update) :
					
					$kidparentsmap_update->nidara_kid_profile_id = $input_data->nidara_kid_profile_id;
					$kidparentsmap_update->users_id = $input_data->users_id;
					if ($kidparentsmap_update->save ()) :
						return $this->response->setJsonContent ( [ 
								'status' => true,
								'message' => 'succefully' 
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
			$kidparentsmap_delete = KidParentsMap::findFirstByid ( $id );
			if ($kidparentsmap_delete) :
				if ($kidparentsmap_delete->delete ()) :
					return $this->response->setJsonContent ( [ 
							'status' => true,
							'Message' => 'Record has been deleted succefully ' 
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
	public function kidinfo() {
		$headers = $this->request->getHeaders ();
		if (empty ( $headers ['Token'] )) {
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Please give the token' 
			] );
		}
		$baseurl = $this->config->baseurl;
		$token_validate = $this->tokenvalidate->tokencheck ( $headers ['Token'], $baseurl );
		if ($token_validate->status != 1) {
				return $this->response->setJsonContent ( [ 
						"status" => false,
						"message" => "Invalid User" 
				] );
			}
		$users_id = $token_validate->user_info->id;
		$getby_userid = KidParentsMap::find ( "users_id=$users_id" )->toArray ();
		if ($getby_userid) {
			$kidaprofiles = array ();
			foreach ( $getby_userid as $key => $value ) {
				$kid_id = $value ['nidara_kid_profile_id'];
				$kid_profile = NidaraKidProfile::findFirst ( $kid_id )->toArray ();
				$kid_profile['nidara_kid_profile_id']=$kid_profile['id'];
				unset($kid_profile['id']);
				if($key == 0){
					$kid_profile['is_default_kid']=1;
				}else{
					$kid_profile['is_default_kid']=0;
				}
				$kid_guide = KidGuidedLearningMap::findFirstBynidara_kid_profile_id ( $kid_id );
				if(!empty($kid_guide)){
				  $kid_profile['package_id']=$kid_guide->id;
				  $guided=GuidedLearning::findFirstByid($kid_guide->guided_learning_id);
			          $kid_profile['package_name']=$guided->learning_model;
				}
				$kidaprofiles [] = $kid_profile;
			}
			
			return $this->response->setJsonContent ( [ 
					'status'=> true,
					'data' =>$kidaprofiles
			] );
			
		} else {
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Data not found' 
			] );
		}
	}
}
