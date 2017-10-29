<?php

use Phalcon\Mvc\Micro;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Digit;
use Phalcon\Validation\Validator\Alpha;
use Phalcon\Validation\Validator\StringLength; 
use Phalcon\Validation\Validator\PresenceOf;

class NidarakidlanguageinfoController extends \Phalcon\Mvc\Controller {

    public function index() {
        
    }

    /**
     * Fetch all Record from database :-
     */
    public function viewall() {
        $subject = NidaraKidLanguageInfo::find();
        if ($subject):
            return Json_encode($subject);
        else:
            return $this->response->setJsonContent(['status' => 'Error', 'Message' => 'Faield']);
        endif;
    }

    /*
     * Fetch Record from database based on ID :-
     */

    public function getbyid() {

        $input_data = $this->request->getJsonRawBody();
        $id = isset($input_data->id) ? $input_data->id : '';
        if (empty($id)):
            return $this->response->setJsonContent(['status' => 'Error', 'message' => 'Invalid input parameter']);
        else:
            $collection = NidaraKidLanguageInfo::findFirstByid($id);
            if ($collection):
                return Json_encode($collection);
            else:
                return $this->response->setJsonContent(['status' => 'Error', 'Message' => 'Data not found']);
            endif;
        endif;
    }

    /**
     * This function using to create NidaraKidLanguageInfo information
     */
    public function create() {

        $input_data = $this->request->getJsonRawBody();

        /**
         * This object using valitaion 
         */
        $validation = new Validation();
        $validation->add('id', new PresenceOf(['message' => 'id is required']));
        $validation->add('id', new Digit(['message'=>'Id is only digit']));
        $validation->add('nidara_kid_profile_id', new PresenceOf(['message' => 'nidara_kid_profile_id is required']));
        $validation->add('nidara_kid_profile_id', new Digit(['message'=>'kid profile id is only Digit']));
        $validation->add('language', new PresenceOf(['message' => 'language is required']));
        $validation->add('language', new Alpha(['message'=>'Language is only letters']));
        $validation->add('language', new StringLength(['max'=>20,'min'=> 2,'messageMaximum' => 'Language is maximum 20',
            'messageMinimum' => 'Language is minimum 2']));
        $validation->add('location', new PresenceOf(['message' => 'location is required']));
        $validation->add('location', new Alpha(['message'=>'Location is only letters']));
        $validation->add('child_understand_english', new PresenceOf(['message' => 'child_understand_english is required']));
        $messages = $validation->validate($input_data);
        if (count($messages)):
            foreach ($messages as $message) :
                $result[] = $message->getMessage();
            endforeach;
            return $this->response->setJsonContent($result);
        else:
            $collection = new NidaraKidLanguageInfo();
            $collection->id = $input_data->id;
            $collection->nidara_kid_profile_id = $input_data->nidara_kid_profile_id;
            $collection->language = $input_data->language;
            $collection->location = $input_data->location;
            $collection->child_understand_english = $input_data->child_understand_english;
            $collection->created_at = date('Y-m-d H:i:s');
            $collection->created_by = 1;
            $collection->modified_at = date('Y-m-d H:i:s');
            if ($collection->save()):
                return $this->response->setJsonContent(['status' => 'Ok', 'message' => 'succefully']);
            else:
                return $this->response->setJsonContent(['status' => 'Error', 'message' => 'Failed']);
            endif;
        endif;
    }

    /**
     * This function using to NidaraKidLanguageInfo information edit
     */
    public function update() {

        $input_data = $this->request->getJsonRawBody();
        $id = isset($input_data->id) ? $input_data->id : '';
        if (empty($id)):
            return $this->response->setJsonContent(['status' => 'Error', 'message' => 'Id is null']);
        else:
            $validation = new Validation();
            $validation->add('nidara_kid_profile_id', new PresenceOf(['message' => 'nidara_kid_profile_idis required']));
            $validation->add('nidara_kid_profile_id', new Digit(['message'=>'kid profile id is only Digit']));
            $validation->add('language', new PresenceOf(['message' => 'languageis required']));
            $validation->add('language', new Alpha(['message'=>'Language is only letters']));
            $validation->add('language', new StringLength(['max'=>20,'min'=> 2,'messageMaximum' => 'Language is maximum 20',
            'messageMinimum' => 'Language is minimum 2']));
            $validation->add('location', new PresenceOf(['message' => 'locationis required']));
            $validation->add('location', new Alpha(['message'=>'Location is only letters']));
            $validation->add('child_understand_english', new PresenceOf(['message' => 'child_understand_englishis required']));
            $messages = $validation->validate($input_data);
            if (count($messages)):
                foreach ($messages as $message):
                    $result[] = $message->getMessage();
                endforeach;
                return $this->response->setJsonContent($result);
            else:
                $collection = NidaraKidLanguageInfo::findFirstByid($id);
                if ($collection):
                    $collection->nidara_kid_profile_id = $input_data->nidara_kid_profile_id;
                    $collection->language = $input_data->language;
                    $collection->location = $input_data->location;
                    $collection->child_understand_english = $input_data->child_understand_english;
                    $collection->created_by = 1;
                    $collection->modified_at = date('Y-m-d H:i:s');
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
            $collection = NidaraKidLanguageInfo::findFirstByid($id);
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
