<?php

use Phalcon\Mvc\Micro;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;

class GamesdatabaseController extends \Phalcon\Mvc\Controller {

    public function index() {
        
    }

    /**
     * Fetch all Record from database :-
     */
    public function viewall() {
        $subject = GamesDatabase::find();
        if ($subject):
            return Json_encode($subject);
        else:
            return $this->response->setJsonContent(['status' => false, 'Message' => 'Faield']);
        endif;
    }

    /*
     * Fetch Record from database based on ID :-
     */

    public function getbyid($id = null) {

        $input_data = $this->request->getJsonRawBody();
        $id = isset($input_data->id) ? $input_data->id : '';
        if (empty($id)):
            return $this->response->setJsonContent(['status' => false, 'message' => 'Invalid input parameter']);
        else:
            $collection = GamesDatabase::findFirstByid($id);
            if ($collection):
                return Json_encode($collection);
            else:
                return $this->response->setJsonContent(['status' => false, 'Message' => 'Data not found']);
            endif;
        endif;
    }

    /**
     * This function using to create GamesDatabase information
     */
    public function create() {

        $input_data = $this->request->getJsonRawBody();

        /**
         * This object using valitaion 
         */
        $validation = new Validation();
        $validation->add('id', new PresenceOf(['message' => 'id is required']));
        $validation->add('game_id', new PresenceOf(['message' => 'game_id is required']));
        $validation->add('status', new PresenceOf(['message' => 'status is required']));
        $validation->add('created_at', new PresenceOf(['message' => 'created_at is required']));
        $validation->add('created_by', new PresenceOf(['message' => 'created_by is required']));
        $validation->add('modified_at', new PresenceOf(['message' => 'modified_at is required']));
        $messages = $validation->validate($input_data);
        if (count($messages)):
            foreach ($messages as $message) :
                $result[] = $message->getMessage();
            endforeach;
            return $this->response->setJsonContent($result);
        else:
            $collection = new GamesDatabase();
            $collection->id = $input_data->id;
            $collection->game_id = $input_data->game_id;
            $collection->status = $input_data->status;
            $collection->created_at = $input_data->created_at;
            $collection->created_by = $input_data->created_by;
            $collection->modified_at = $input_data->modified_at;
            if ($collection->save()):
                return $this->response->setJsonContent(['status' => true, 'message' => 'succefully']);
            else:
                return $this->response->setJsonContent(['status' => false, 'message' => 'Failed']);
            endif;
        endif;
    }

    /**
     * This function using to GamesDatabase information edit
     */
    public function update($id = null) {

        $input_data = $this->request->getJsonRawBody();
        $id = isset($input_data->id) ? $input_data->id : '';
        if (empty($id)):
            return $this->response->setJsonContent(['status' => false, 'message' => 'Id is null']);
        else:
            $validation = new Validation();
            $validation->add('id', new PresenceOf(['message' => 'id is required']));
            $validation->add('game_id', new PresenceOf(['message' => 'game_idis required']));
            $validation->add('status', new PresenceOf(['message' => 'statusis required']));
            $validation->add('created_at', new PresenceOf(['message' => 'created_atis required']));
            $validation->add('created_by', new PresenceOf(['message' => 'created_byis required']));
            $validation->add('modified_at', new PresenceOf(['message' => 'modified_atis required']));
            $messages = $validation->validate($input_data);
            if (count($messages)):
                foreach ($messages as $message):
                    $result[] = $message->getMessage();
                endforeach;
                return $this->response->setJsonContent($result);
            else:
                $collection = GamesDatabase::findFirstByid($id);
                if ($collection):
                    $collection->id = $input_data->id;
                    $collection->game_id = $input_data->game_id;
                    $collection->status = $input_data->status;
                    $collection->created_at = $input_data->created_at;
                    $collection->created_by = $input_data->created_by;
                    $collection->modified_at = $input_data->modified_at;
                    if ($collection->save()):
                        return $this->response->setJsonContent(['status' => true, 'message' => 'succefully']);
                    else:
                        return $this->response->setJsonContent(['status' => false, 'message' => 'Failed']);
                    endif;
                else:
                    return $this->response->setJsonContent(['status' => false, 'message' => 'Invalid id']);
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
			$collection = GamesDatabase::findFirstByid ( $id );
			if ($collection) :
				if ($collection->delete ()) :
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
						'Message' => 'ID doesn\'t']);
            endif;
        endif;
    }
    
    /**
     * Save game result
     */
	public function savegamesresult() {
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
			if (empty ( $input_data )) {
				return $this->response->setJsonContent ( [ 
						'status' => false,
						'message' => 'Please give the same result'
				] );
			}
			$validation = new Validation ();
			$validation->add ( 'game_id', new PresenceOf ( [ 
					'message' => 'Game id is required' 
			] ) );
			$validation->add ( 'nidara_kid_profile_id', new PresenceOf ( [ 
					'message' => 'Please give the kid id' 
			] ) );
			$validation->add ( 'answers', new PresenceOf ( [ 
					'message' => 'Please give the answers' 
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
			foreach ( $input_data->answers as $answer ) {
				if(!isset($answer->options)){
					return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => "Please give the options"
					] );	
				}
				foreach ( $answer->options as $option ) {
					$optionobj =Options::findFirstByid($option);
					$answers = new Answers ();
					$answers->id = $this->gamesidgen->getNewId ( 'answers' );
					$answers->questions_id = $answer->question_id;
					$answers->session_id = $input_data->session_id;
					$answers->is_correct = $optionobj->is_answer;
					$answers->options_id = $option;
					$answers->nidara_kid_profile_id = $input_data->nidara_kid_profile_id;
					$answers->created_at = date ( 'Y-m-d H:i:s' );
					$answers->created_by = 1;
					$answers->save ();
				}
			}
			// Save the result status for kid
			$kidstatus = KidsGamesStatus::findFirstBynidara_kid_profile_id ( $input_data->nidara_kid_profile_id );
			if (! $kidstatus) {
				$kidstatus = new KidsGamesStatus ();
				$kidstatus->id = $this->gamesidgen->getNewId ( 'kidgamestatus' );
				$kidstatus->nidara_kid_profile_id = $input_data->nidara_kid_profile_id;
			}
			$gamemapid=$this->getGuidedLearningId($input_data->game_id);
			$kidstatus->guided_learning_games_map_id = $gamemapid->guided_learning_schedule_id;
			if ($input_data->current_status == 'quit') {
				$kidstatus->current_status = "quit";
			} else {
				$kidstatus->current_status = "completed";
			}
			$kidstatus->save ();
			
			return $this->response->setJsonContent ( [ 
					'status' => true,
					'message' => 'Game saved successfully' 
			] );
		} catch ( Exception $e ) {echo $e->getMessage();
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'message' => 'Error while saving the datas' 
			] );
		}
	}
	
	/**
	 * Get Game Map id
	 * @param integer $gameid
	 * @return array
	 */
	public function getGuidedLearningId($gameid){
		$gamemap = $this->modelsManager->createBuilder ()->columns ( array (
				'GuidedLearningGamesMap.id',
				'GuidedLearningGamesMap.guided_learning_schedule_id',
		) )->from ( 'GamesDatabase' )
		->join ( 'GamesTagging', 'GamesTagging.games_database_id=GamesDatabase.id' )
		->join ( 'GuidedLearningGamesMap', 'GuidedLearningGamesMap.games_tagging_id=GamesTagging.id' )
		->inwhere ( "GamesDatabase.game_id", array (
				$gameid
		) )->getQuery ()->execute ();
		$guided_learning_map = array ();
		foreach ( $gamemap as $guided_learning_map ) {
			return $guided_learning_map;
		}
	}
	
	/**
	 * Get answer status
	 * @param object $answer
	 * @return number
	 */
	public function getlessonstatus($answer) {
		$gamestatus = $this->modelsManager->createBuilder ()->columns ( array (
				'Questions.id as question_id',
				'Options.id as option_id',
				"Options.is_answer",
				"Options.is_multi_answer" 
		) )->from ( 'Questions' )->leftjoin ( 'Options', 'Options.questions_id=Questions.id' )
		->inwhere ( "Questions.id", array (
				$answer->question_id 
		) )->inwhere ( "Options.id", $answer->options )->getQuery ()->execute ();
		$wrong_answer = 0;
		foreach ( $gamestatus as $game ) {
			if (empty ( $game->is_answer )) {
				$wrong_answer ++;
			}
		}
		return $wrong_answer;
	}
	
	/**
	 * Get Game info
	 * @return string
	 */
	public function getgameinfobygameid() {
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
			$game_id = isset ( $input_data->game_id ) ? $input_data->game_id : '';
			if (empty ( $game_id )) {
				return $this->response->setJsonContent ( [ 
						'status' => false,
						'Message' => 'Please give the game id' 
				] );
			}
			if (empty ( $input_data->nidara_kid_profile_id )) {
				return $this->response->setJsonContent ( [ 
						'status' => false,
						'Message' => 'Please give the kid id' 
				] );
			}
			$gamedatabase = $this->modelsManager->createBuilder ()->columns ( array (
					'Questions.id as question_id',
					'GamesDatabase.game_type',
			) )->from ( 'Questions' )
			->leftjoin ( 'QuestionsTagging', 'QuestionsTagging.questions_id=Questions.id' )
			->leftjoin ( 'Indicators', 'QuestionsTagging.indicators_id=Indicators.id' )
			->leftjoin ( 'GamesTagging', 'Indicators.id=GamesTagging.indicators_id' )
			->leftjoin ( 'GamesDatabase', 'GamesTagging.games_database_id=GamesDatabase.id' )
			->orderBy('Questions.id')
			->inwhere ( "GamesDatabase.game_id", array (
					$game_id 
			) )->getQuery ()->execute ();
			$gamedatas = array ();
			$i = 1;
			$game_name = $this->getGameNameByGameId ( $game_id );
			foreach ( $gamedatabase as $gamedata ) {
				$gamedatas ['gametype'] = $gamedata->game_type;
				$options = Options::findByquestions_id($gamedata->question_id);
				$optionssdataarray=array();
				foreach ( $options as $option ) {
					$optionssdata['option_id'] = $option->id;
					$optionssdata['option'] = $option->option;
					$optionssdata['is_correct'] = $option->is_answer;
					$optionssdata['is_image'] = $option->is_image;
					$optionssdata['image_path'] = strtolower(str_replace(" ","_",$game_name))."/".$option->image_path;
					$optionssdataarray[]=$optionssdata;
				}
				$questionsdata ['options'] = $optionssdataarray;
				$questionsdata ['questions_id'] = $gamedata->question_id;
				if ($game_name == 'A') {
					$gamedatas ['words'] [6] = $questionsdata;
				}else{
					$gamedatas ['words'] [$i] = $questionsdata;
				}
				$i ++;
				unset($gamedata->game_type);
			}
			$session_id = $this->getSessionId ( $game_id, $input_data->nidara_kid_profile_id );
			$json = file_get_contents ( APP_PATH . "/library/gamesdata/games.json" );
			$replacedata = array (	
					"{{gamename}}" => $game_name,
					"$" => strtolower ( $game_name ) 
			);
			$json = str_replace ( array_keys ( $replacedata ), array_values ( $replacedata ), $json );
			$gamejsondata = json_decode ( $json, true );
			$gameinfodatas = array ();
			foreach ( $gamejsondata ['GAMEINFO'] as $gameinfo ) {
				$gamesjson = array ();
				if (trim($gameinfo ['gameType']) == trim($gamedatas ['gametype'])) {
					$p = 1;
					foreach ( $gameinfo ['slideData'] as $slidedata ) {
						$game_options = array (
								'words'=>
								 $gamedatas ['words'][$p]
						);
						$gamesjson [] = array_merge ( $slidedata,$game_options);
						$p ++;
					}
					$gameinfo ['slideData'] = $gamesjson;
				}
				$gameinfodatas [] = $gameinfo;
			}
			$gamejsondata['game_id'] = $game_id;
			$gamejsondata['session_id'] = $session_id;
			$gamejsondata ['GAMEINFO'] = array_values($gameinfodatas);
			return $this->response->setJsonContent ( $gamejsondata );
		} catch ( Exception $e ) {
			return $this->response->setJsonContent ([ 
					'status' => false,
					'message' => 'Error while getting the datas'.$e->getMessage() 
			]);
		}
	}
	
	/**
	 * Save game history
	 */
	public function getSessionId($gameid, $kidid) {
		$gamemapid = $this->getGuidedLearningId ( $gameid );
		$gamehistory = new GameHistory ();
		$gamehistory->id = $this->gamesidgen->getNewId ( 'gameshistory' );
		$gamehistory->session_id = uniqid ();
		$gamehistory->nidara_kid_profile_id = $kidid;
		$gamehistory->guided_learning_games_map_id = $gamemapid->guided_learning_schedule_id;
		$gamehistory->created_at = date ( 'Y-m-d H:i:s' );
		$gamehistory->created_by = 1;
		$gamehistory->save ();
		return $gamehistory->session_id;
	}
	
	/**
	 * Get Game name by game id
	 * @param string $gameid
	 * @return value
	 */
	public function getGameNameByGameId($gameid) {
		$gamearray = array (
				"5992eaf7c114b" => "A",
				"599006c9dd128" => "one",
				"599007029d6df" => "five",
				"599c561d37d82" => "Emotion motion",
				"599c54be3f0f1" => "Nutrition",
				"599c5402348c6" => "Yoga",
				"599c51fc3b5b4" => "Brain Game",
				"599c515ea3952" => "Write Number 5",
				"599c446c91889" => "Dinosaurs",
				"59903520328af" => "In The Sky",
				"599033e356d49" => "My Body",
				"59900e7c907ee" => "Big and Small"
		);
		return $gamearray [$gameid];
	}
	
	/**
	 * Dummy function for test
	 */
	public function dummydata() {
		$json = file_get_contents ( APP_PATH . "/library/gamesdata/games.json" );
		return $this->response->setJsonContent ( json_decode ( $json, true ) );
	}
	
}
