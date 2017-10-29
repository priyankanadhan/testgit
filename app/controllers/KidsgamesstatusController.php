<?php

use Phalcon\Mvc\Micro;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;

class KidsgamesstatusController extends \Phalcon\Mvc\Controller {

    public function index() {
        
    }

    /**
     * Fetch all Record from database :-
     */
    public function viewall() {
        $subject = KidsGamesStatus::find();
        if ($subject):
            return Json_encode($subject);
        else:
            return $this->response->setJsonContent(['status' => 'Error', 'Message' => 'Faield']);
        endif;
    }

    /*
     * Fetch Record from database based on ID :-
     */

    public function getbyid($id = null) {

        $input_data = $this->request->getJsonRawBody();
        $id = isset($input_data->id) ? $input_data->id : '';
        if (empty($id)):
            return $this->response->setJsonContent(['status' => 'Error', 'message' => 'Invalid input parameter']);
        else:
            $collection = KidsGamesStatus::findFirstByid($id);
            if ($collection):
                return Json_encode($collection);
            else:
                return $this->response->setJsonContent(['status' => 'Error', 'Message' => 'Data not found']);
            endif;
        endif;
    }

    /**
     * This function using to create KidsGamesStatus information
     */
    public function create() {

        $input_data = $this->request->getJsonRawBody();

        /**
         * This object using valitaion 
         */
        $validation = new Validation();
        $validation->add('nidara_kid_profile_id', new PresenceOf(['message' => 'nidara_kid_profile_id is required']));
        $validation->add('guided_learning_games_map_id', new PresenceOf(['message' => 'guided_learning_games_map_id is required']));
        $validation->add('current_status', new PresenceOf(['message' => 'current_status is required']));
        $messages = $validation->validate($input_data);
		if (count ( $messages )) {
			foreach ( $messages as $message ) :
				$result [] = $message->getMessage ();
			endforeach
			;
			return $this->response->setJsonContent ( $result );
		} else {
			$kidsgamesstatus = new KidsGamesStatus ();
			$kidsgamesstatus->id = $input_data->id;
			$kidsgamesstatus->nidara_kid_profile_id = $input_data->nidara_kid_profile_id;
			$kidsgamesstatus->guided_learning_games_map_id = $input_data->guided_learning_games_map_id;
			$kidsgamesstatus->current_status = $input_data->current_status;
			if ($kidsgamesstatus->save ()) {
				$gamehistory = new GameHistory ();
				$gamehistory->id = $input_data->id;	
				$gamehistory->session_id = $input_data->session_id;
				$gamehistory->guided_learning_games_map_id = $input_data->guided_learning_games_map_id;
				$gamehistory->nidara_kid_profile_id = $input_data->nidara_kid_profile_id;
				$gamehistory->created_at = date('Y-m-d H:i:s');
				$gamehistory->created_by = 1;
				if ($gamehistory->save ()) {
					return $this->response->setJsonContent ([ 
							'status' => true,
							'message' => 'successfully' 
					]);
				} else {
					return $this->response->setJsonContent ([ 
							'status' => false,
							'message' => 'Failed' 
					]);
				}
			}
		}
    }

    /**
     * This function using to KidsGamesStatus information edit
     */
    public function update($id = null) {

        $input_data = $this->request->getJsonRawBody();
        $id = isset($input_data->id) ? $input_data->id : '';
        if (empty($id)):
            return $this->response->setJsonContent(['status' => 'Error', 'message' => 'Id is null']);
        else:
            $validation = new Validation();
            $validation->add('id', new PresenceOf(['message' => 'idis required']));
            $validation->add('nidara_kid_profile_id', new PresenceOf(['message' => 'nidara_kid_profile_idis required']));
            $validation->add('guided_learning_games_map_id', new PresenceOf(['message' => 'guided_learning_games_map_idis required']));
            $validation->add('current_status', new PresenceOf(['message' => 'current_statusis required']));
            $messages = $validation->validate($input_data);
            if (count($messages)):
                foreach ($messages as $message):
                    $result[] = $message->getMessage();
                endforeach;
                return $this->response->setJsonContent($result);
            else:
                $collection = KidsGamesStatus::findFirstByid($id);
                if ($collection):
                    $collection->id = $input_data->id;
                    $collection->nidara_kid_profile_id = $input_data->nidara_kid_profile_id;
                    $collection->guided_learning_games_map_id = $input_data->guided_learning_games_map_id;
                    $collection->current_status = $input_data->current_status;
                    if ($collection->save()):
                        return $this->response->setJsonContent(['status' => 'Ok', 'message' => 'succefully']);
                    else:
                        return $this->response->setJsonContent(['status' => 'Error', 'message' => 'Failed']);
                    endif;
                else:
                    return $this->response->setJsonContent(['status' => 'Error', 'message' => 'Invalid id']);
                endif;
            endif;
        endif;
    }

    /**
     * This function using delete kids caregiver information
     */
    public function delete() {

        $input_data = $this->request->getJsonRawBody();
        $id = isset($input_data->id) ? $input_data->id : '';
        if (empty($id)):
            return $this->response->setJsonContent(['status' => 'Error', 'message' => 'Id is null']);
        else:
            $collection = KidsGamesStatus::findFirstByid($id);
            if ($collection):
                if ($collection->delete()):
                    return $this->response->setJsonContent(['status' => 'OK', 'Message' => 'Record has been deleted succefully ']);
                else:
                    return $this->response->setJsonContent(['status' => 'Error', 'Message' => 'Data could not be deleted']);
                endif;
            else:
                return $this->response->setJsonContent(['status' => 'Error', 'Message' => 'ID doesn\'t']);
            endif;
        endif;
    }

}