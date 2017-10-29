<?php
use Phalcon\Mvc\Micro;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
class GuidedlearningscheduleController extends \Phalcon\Mvc\Controller {
	public function index() {
	}
	
	/**
	 * Fetch all Record from database :-
	 */
	public function viewall() {
		$guidedlearningschedule = GuidedLearningSchedule::find ();
		if ($guidedlearningschedule) :
			return Json_encode ( $guidedlearningschedule );
		 else :
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'Message' => 'Failed' 
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
			$guidedlearning_getby_id = GuidedLearningSchedule::findFirstByid ( $id );
			if ($guidedlearning_getby_id) :
				return Json_encode ( $guidedlearning_getby_id );
			 else :
				return $this->response->setJsonContent ( [ 
						'status' => false,
						'Message' => 'Data not found' 
				] );
			endif;
		endif;
	}
	
	/**
	 * This function using to create GuidedLearningSchedule information
	 */
	public function create() {
		$input_data = $this->request->getJsonRawBody ();
		
		/**
		 * This object using valitaion
		 */
		$validation = new Validation ();
		$validation->add ( 'schedule_identified', new PresenceOf ( [ 
				'message' => 'Schedule identified is required' 
		] ) );
		$validation->add ( 'order_by', new PresenceOf ( [ 
				'message' => 'order_by is required' 
		] ) );
		$validation->add ( 'guided_learning_id', new PresenceOf ( [ 
				'message' => 'Guided learning id is required' 
		] ) );
		$messages = $validation->validate ( $input_data );
		if (count ( $messages )) :
			foreach ( $messages as $message ) :
				$result [] = $message->getMessage ();
			endforeach
			;
			return $this->response->setJsonContent ( $result );
		 else :
			$guidedlearning_create = new GuidedLearningSchedule ();
			$guidedlearning_create->id = $input_data->id;
			$guidedlearning_create->schedule_identified = $input_data->schedule_identified;
			$guidedlearning_create->order_by = $input_data->order_by;
			$guidedlearning_create->guided_learning_id = $input_data->guided_learning_id;
			$guidedlearning_create->created_at = date ( 'Y-m-d H:i:s' );
			$guidedlearning_create->created_by = 1;
			$guidedlearning_create->modified_at = date ( 'Y-m-d H:i:s' );
			if ($guidedlearning_create->save ()) :
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
	 * This function using to GuidedLearningSchedule information edit
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
			$validation->add ( 'schedule_identified', new PresenceOf ( [ 
					'message' => 'Schedule identifiedis required' 
			] ) );
			$validation->add ( 'order_by', new PresenceOf ( [ 
					'message' => 'order_byis required' 
			] ) );
			$validation->add ( 'guided_learning_id', new PresenceOf ( [ 
					'message' => 'Guided learning id is required' 
			] ) );
			$messages = $validation->validate ( $input_data );
			if (count ( $messages )) :
				foreach ( $messages as $message ) :
					$result [] = $message->getMessage ();
				endforeach
				;
				return $this->response->setJsonContent ( $result );
			 else :
				$guidedLearning_update = GuidedLearningSchedule::findFirstByid ( $id );
				if ($guidedLearning_update) :
					$guidedLearning_update->schedule_identified = $input_data->schedule_identified;
					$guidedLearning_update->order_by = $input_data->order_by;
					$guidedLearning_update->guided_learning_id = $input_data->guided_learning_id;
					$guidedLearning_update->created_by = $id;
					$guidedLearning_update->modified_at = date ( 'Y-m-d H:i:s' );
					if ($guidedLearning_update->save ()) :
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
			$guidedlearning_delete = GuidedLearningSchedule::findFirstByid ( $id );
			if ($guidedlearning_delete) :
				if ($guidedlearning_delete->delete ()) :
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
	
	/**
	 * 
	 * @return string
	 */
	public function getkidgames() {
// 		$headers = $this->request->getHeaders ();
// 		if (empty ( $headers ['Token'] )) {
// 			return $this->response->setJsonContent ( [
// 					"status" => false,
// 					"message" => "Please give the token"
// 			] );
// 		}
// 		$baseurl = $this->config->baseurl;
// 		$token_check = $this->tokenvalidate->tokencheck ( $headers ['Token'], $baseurl );
// 		if ($token_check->status != 1) {
// 			return $this->response->setJsonContent ( [
// 					"status" => false,
// 					"message" => "Invalid User"
// 			] );
// 		}
		$input_data = $this->request->getJsonRawBody ();
		$id = isset ( $input_data->kid_id ) ? $input_data->kid_id : '';
		if (empty ( $id )) {
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Please give the kid id' 
			] );
		} else {
			$games_database = $this->modelsManager->createBuilder ()->columns ( array (
					'GuidedLearningSchedule.id as guided_id',
					'GamesDatabase.id',
					'GamesDatabase.game_id',
					'GamesDatabase.games_name',
					'GamesDatabase.games_folder',
					'GamesDatabase.status',
					'GamesTagging.indicators_id',
					'GuidedLearningSchedule.schedule_identified',
			) )->from ( 'KidGuidedLearningMap' )
			->join ( 'GuidedLearning', 'KidGuidedLearningMap.guided_learning_id=GuidedLearning.id' )
			->join ( 'GuidedLearningSchedule', 'GuidedLearningSchedule.guided_learning_id=GuidedLearning.id' )
			->join ( 'GuidedLearningGamesMap', 'GuidedLearningGamesMap.guided_learning_schedule_id=GuidedLearningSchedule.id' )
			->join ( 'GamesTagging', 'GamesTagging.id=GuidedLearningGamesMap.games_tagging_id' )
			->join ( 'GamesDatabase', 'GamesTagging.games_database_id=GamesDatabase.id' )
			->groupBy ("GamesDatabase.id")
			->where ( "KidGuidedLearningMap.nidara_kid_profile_id", array (
					$id 
			) )->getQuery ()->execute ();
			$games = array ();
			$i=1;
			$gamecolor = GameColors::findFirstByday ( date('l') );
			$games ['background_image'] = $gamecolor->background_color;
			foreach ( $games_database as $games_data ) {
				$games_data->routerLink = str_replace ( "_ ", "", strtolower ( $games_data->games_name ) );
				$games_data->routerLink = str_replace ( " ", "_", strtolower ( $games_data->games_name ) );
				$games_data->games_folder = '/' . ltrim ( $games_data->games_folder, '/' );
				$games_data_array [] = $games_data;
				$i++;
			}
			$chunked_array = array_chunk ( $games_data_array, 4 );
			array_replace ( $chunked_array, $chunked_array );
			$keyed_array = array ();
			foreach ( $chunked_array as $chunked_arrays ) {
				$keyed_array [] ['page'] = $chunked_arrays;
			}
			$games ['games'] = $keyed_array;
			return Json_encode ( $games );
		}
	}
}
