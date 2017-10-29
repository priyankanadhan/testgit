<?php
use Phalcon\Mvc\Micro;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
class CoreframeworksController extends \Phalcon\Mvc\Controller {
	public function index() {
	}
	/**
	 * Fetch all Record from database :-
	 */
	public function viewall() {
		$core_frm = CoreFrameworks::find ();
		if ($core_frm) :
			return Json_encode ( $core_frm );
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
			$core_frm_getbyid = CoreFrameworks::findFirstByid ( $id );
			if ($core_frm_getbyid) :
				return Json_encode ( $core_frm_getbyid );
			 else :
				return $this->response->setJsonContent ( [ 
						'status' => false,
						'Message' => 'Data not found' 
				] );
			endif;
		endif;
	}
	/**
	 * This function using to create CoreFrameworks information
	 */
	public function create() {
		$input_data = $this->request->getJsonRawBody ();
		
		/**
		 * This object using valitaion
		 */
		$validation = new Validation ();
		$validation->add ( 'name', new PresenceOf ( [ 
				'message' => 'name is required' 
		] ) );
		$validation->add ( 'status', new PresenceOf ( [ 
				'message' => 'status is required' 
		] ) );
		$messages = $validation->validate ( $input_data );
		if (count ( $messages )) :
			foreach ( $messages as $message ) :
				$result [] = $message->getMessage ();
			endforeach
			;
			return $this->response->setJsonContent ( $result );
		 else :
			$core_frm_create = new CoreFrameworks ();
			$core_frm_create->id = $input_data->id;
			$core_frm_create->name = $input_data->name;
			$core_frm_create->status = $input_data->status;
			$core_frm_create->created_at = date ( 'Y-m-d H:i:s' );
			$core_frm_create->created_by = 1;
			if ($core_frm_create->save ()) :
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
	 * This function using to CoreFrameworks information edit
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
			$validation->add ( 'name', new PresenceOf ( [ 
					'message' => 'name is required' 
			] ) );
			$validation->add ( 'status', new PresenceOf ( [ 
					'message' => 'status is required' 
			] ) );
			$messages = $validation->validate ( $input_data );
			if (count ( $messages )) :
				foreach ( $messages as $message ) :
					$result [] = $message->getMessage ();
				endforeach
				;
				return $this->response->setJsonContent ( $result );
			 else :
				$core_frm_update = CoreFrameworks::findFirstByid ( $id );
				if ($core_frm_update) :
					$core_frm_update->name = $input_data->name;
					$core_frm_update->status = $input_data->status;
					$core_frm_update->created_by = $id;
					if ($core_frm_update->save ()) :
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
			$core_frm_delete = CoreFrameworks::findFirstByid ( $id );
			if ($core_frm_delete) :
				if ($core_frm_delete->delete ()) :
					return $this->response->setJsonContent ( [ 
							'status' => true,
							'Message' => 'Record has been deleted successfully ' 
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
	 * Get core frameworks details
	 * @return array
	 */
	public function getcoreframeworks() {
		$input_data = $this->request->getJsonRawBody ();
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
		$id = isset ( $input_data->nidara_kid_profile_id ) ? $input_data->nidara_kid_profile_id : '';
		if(empty($id)){
			return $this->response->setJsonContent ( [
					'status' => false,
					'message' => 'Kid Id is null'
			] );
		}
		$games_database = $this->modelsManager->createBuilder ()->columns ( array (
				'GuidedLearningSchedule.id as guided_id',
				'GamesDatabase.id',
				'GamesDatabase.game_id',
				'GamesDatabase.games_name',
				'GamesDatabase.games_folder',
				'GamesDatabase.status',
				'GamesDatabase.daily_tips',
				'GamesTagging.indicators_id',
				'GuidedLearningSchedule.schedule_identified',
				'CoreFrameworks.name as core_framework_name',
				'Subject.subject_name',
		) )->from ( 'KidGuidedLearningMap' )
		->leftjoin ( 'GuidedLearning', 'KidGuidedLearningMap.guided_learning_id=GuidedLearning.id' )
		->leftjoin ( 'GuidedLearningSchedule', 'GuidedLearningSchedule.guided_learning_id=GuidedLearning.id' )
		->leftjoin ( 'GuidedLearningGamesMap', 'GuidedLearningGamesMap.guided_learning_schedule_id=GuidedLearningSchedule.id' )
		->leftjoin ( 'GamesTagging', 'GamesTagging.id=GuidedLearningGamesMap.games_tagging_id' )
		->leftjoin ( 'GamesDatabase', 'GamesTagging.games_database_id=GamesDatabase.id' )
		->leftjoin ( 'StandardIndicatorsMap', 'StandardIndicatorsMap.indicators_id=GamesTagging.indicators_id' )
		->leftjoin ( 'StandardSubject', 'StandardIndicatorsMap.standard_id=StandardSubject.standard_id' )
		->leftjoin ( 'Subject', 'Subject.id=StandardSubject.subject_id' )
		->leftjoin ( 'CoreFrameworksSubjectMap', 'Subject.id=CoreFrameworksSubjectMap.subject_id' )
		->leftjoin ( 'CoreFrameworks', 'CoreFrameworks.id=CoreFrameworksSubjectMap.core_framework_id' )
		->groupBy ("GamesDatabase.id")
		->where ( "KidGuidedLearningMap.nidara_kid_profile_id", array (
				$id
		) )->getQuery ()->execute ();
		
		$core_array = array ();
		foreach ( $games_database as $core_data ) {
			if(!empty($core_data->game_id)){
			$gamepercentage = $this->getgamepercentage ( $core_data->game_id,$id );
			if(!empty($gamepercentage)){
			  $core_data->kid_played=TRUE;
			  if($gamepercentage > 90){
				$core_data->grade_color="#32CD32";
			  }elseif($gamepercentage > 80){
				$core_data->grade_color="#FFFF00";
			  }elseif($gamepercentage > 70){
				$core_data->grade_color="#FFDAB9";
                          }else{
				$core_data->grade_color="#FF0000";
			  }
			}else{
			 $core_data->daily_tips="Game not played";
			}
			$core_data->game_percentage=$gamepercentage;
			$core_framework_name = strtolower( str_replace ( ' ', '_', $core_data->core_framework_name ) );
			$core_array [] = $core_data->core_framework_name;
			$core_frm_array [$core_framework_name] [] = $core_data;
			}
		}
		$core_frame = CoreFrameworks::find ();
		foreach ( $core_frame as $core ) {
			if (! in_array ( $core->name, $core_array )) {
				$core->name = strtolower( str_replace ( ' ', '_', $core->name ) );
				$core_frm_array [$core->name] = array ();
			}
		}
		$kid=NidaraKidProfile::findFirstByid($id);
		if(!empty($kid)){
		$core_frm_array['kid_name']=$kid->first_name;
		}
		$core_frm_array['today_date']=date('l, F d, Y');
		return $this->response->setJsonContent ( [
				'status' => true,
				'data' => $core_frm_array
		] );
		
		
	}

	/**
	 * 
	 * @param unknown $game_id
	 * @return multitype:unknown
	 */
	public function getgamepercentage($game_id,$kid_id){
		$gamedatabase = $this->modelsManager->createBuilder ()->columns ( array (
					'Questions.id as question_id',
					'Answers.id as answer_id',
					'Answers.options_id as options_id',
					'Answers.session_id as session_id',
					'Answers.is_correct as is_correct',
			) )->from ( 'Questions' )
			->leftjoin ( 'Answers', 'Answers.questions_id=Questions.id' )
			->leftjoin ( 'QuestionsTagging', 'QuestionsTagging.questions_id=Questions.id' )
			->leftjoin ( 'Indicators', 'QuestionsTagging.indicators_id=Indicators.id' )
			->leftjoin ( 'GamesTagging', 'Indicators.id=GamesTagging.indicators_id' )
			->leftjoin ( 'GamesDatabase', 'GamesTagging.games_database_id=GamesDatabase.id' )
			->groupBy ("Answers.questions_id")
			->inwhere ( "Answers.nidara_kid_profile_id", array (
					$kid_id 
			) )
			->inwhere ( "GamesDatabase.game_id", array (
					$game_id 
			) )->getQuery ()->execute ();
			$gamedatabasearray=array();
			$percentage=0;
			foreach ( $gamedatabase as $gamedata ) {
			$options = $this->modelsManager->createBuilder ()->columns ( array (
					'Options.id as option_id' 
			) )->from ( 'Options' )->inwhere ( "Options.questions_id", array (
					$gamedata->question_id 
			) )->inwhere ( "Options.is_answer", array (
					1 
			) )->getQuery ()->execute ();
			$answers = $this->modelsManager->createBuilder ()->columns ( array (
					'Answers.id as answer_id'
			) )->from ( 'Answers' )
			->inwhere ( "Answers.questions_id", array (
					$gamedata->question_id
			) )->inwhere ( "Answers.is_correct", array (
					1
			) )->inwhere ( "Answers.session_id", array (
					$gamedata->session_id 
			) )->getQuery ()->execute ();
			$percentage += round((count($answers)/count($options))*100);
			$gamedatabasearray [] = $gamedata;
			}
			if(!empty($gamedatabasearray)){
			$totalpercentage=round($percentage/count($gamedatabasearray),1);
			}
			return $totalpercentage;
	}
}
