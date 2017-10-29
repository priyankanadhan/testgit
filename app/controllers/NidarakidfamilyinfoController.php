<?php

use Phalcon\Mvc\Micro;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Digit;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Alpha;
class NidarakidfamilyinfoController extends \Phalcon\Mvc\Controller {

    public function index() {
        
    }

    /**
     * Fetch all Record from database :-
     */
    public function viewall() {
        $subject = NidaraKidFamilyInfo::find();
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
            $collection = NidaraKidFamilyInfo::findFirstByid($id);
            if ($collection):
                return Json_encode($collection);
            else:
                return $this->response->setJsonContent(['status' => 'Error', 'Message' => 'Data not found']);
            endif;
        endif;
    }

    /**
     * This function using to create NidaraKidFamilyInfo information
     */
    public function create() {

        $input_data = $this->request->getJsonRawBody();

        /**
         * This object using valitaion 
         */
        $validation = new Validation();
        $validation->add('nidara_kid_profile_id', new PresenceOf(['message' => 'nidara_kid_profile_id is required']));
        $validation->add('nidara_kid_profile_id', new Digit(['message'=>'kid profile id is only Digit']));
        $messages = $validation->validate($input_data);
        if (count($messages)):
            foreach ($messages as $message) :
                $result[] = $message->getMessage();
            endforeach;
            return $this->response->setJsonContent($result);
        else:
            $collection = new NidaraKidFamilyInfo();
            $collection->id = $input_data->id;
            $collection->nidara_kid_profile_id = $input_data->nidara_kid_profile_id;
            $collection->mother = $input_data->mother;
            $collection->father = $input_data->father;
            $collection->grandfather = $input_data->grandfather;
            $collection->grandmother = $input_data->grandmother;
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
     * This function using to NidaraKidFamilyInfo information edit
     */
    public function update() {

        $input_data = $this->request->getJsonRawBody();
        $id = isset($input_data->id) ? $input_data->id : '';
        if (empty($id)):
            return $this->response->setJsonContent(['status' => 'Error', 'message' => 'Id is null']);
        else:
            $validation = new Validation();
            $validation->add('nidara_kid_profile_id', new PresenceOf(['message' => 'nidara_kid_profile_id is required']));
            $validation->add('nidara_kid_profile_id', new Digit(['message'=>'kid profile id is only Digit']));
            $validation->add('mother', new PresenceOf(['message' => 'mother is required']));
            $validation->add('mother', new Alpha(['message'=>'mother Name is only letters']));
            $validation->add('mother', new StringLength(['max'=>20,'min'=> 2,'messageMaximum' => 'Mother name is maximum 20',
                'messageMinimum' => 'Mother Name is minimum 2']));
            $validation->add('father', new PresenceOf(['message' => 'father is required']));
            $validation->add('father', new Alpha(['message'=>'father Name is only letters']));
            $validation->add('father', new StringLength(['max'=>20,'min'=> 2,'messageMaximum' => 'father Name is maximum 20',
                'messageMinimum' => 'father Name is minimum 2']));
            $validation->add('grandfather', new PresenceOf(['message' => 'grandfather is required']));
            $validation->add('grandfather', new Alpha(['message'=>'grandfather Name is only letters']));
            $validation->add('grandfather', new StringLength(['max'=>20,'min'=> 2,'messageMaximum' => 'grandfather Name is maximum 20',
                'messageMinimum' => 'Name is minimum 2']));
            $validation->add('grandmother', new PresenceOf(['message' => 'grandmother is required']));
            $validation->add('grandmother', new Alpha(['message'=>'grandmother Name is only letters']));
            $validation->add('grandmother', new StringLength(['max'=>20,'min'=> 2,'messageMaximum' => 'grandmother Name is maximum 20',
                'messageMinimum' => 'grandmother Name is minimum 2']));
            $messages = $validation->validate($input_data);
            if (count($messages)):
                foreach ($messages as $message):
                    $result[] = $message->getMessage();
                endforeach;
                return $this->response->setJsonContent($result);
            else:
                $collection = NidaraKidFamilyInfo::findFirstByid($id);
                if ($collection):
                    $collection->nidara_kid_profile_id = $input_data->nidara_kid_profile_id;
                    $collection->mother = $input_data->mother;
                    $collection->father = $input_data->father;
                    $collection->grandfather = $input_data->grandfather;
                    $collection->grandmother = $input_data->grandmother;
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
            $collection = NidaraKidFamilyInfo::findFirstByid($id);
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
