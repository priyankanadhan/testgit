<?php

use Phalcon\Mvc\Micro;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Digit;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Alpha;
class NidarakidcaregiverinfoController extends \Phalcon\Mvc\Controller {

    public function index() {
        
    }

    /**
     * Fetch all Record from database :-
     */
    public function viewall() {
        $subject = NidaraKidCaregiverInfo::find();
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
            $collection = NidaraKidCaregiverInfo::findFirstByid($id);
            if ($collection):
                return Json_encode($collection);
            else:
                return $this->response->setJsonContent(['status' => 'Error', 'Message' => 'Data not found']);
            endif;
        endif;
    }

    /**
     * This function using to create NidaraKidCaregiverInfo information
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
        $validation->add('name', new PresenceOf(['message' => 'name is required']));
        $validation->add('name', new Alpha(['message'=>'Name is only letters']));
        $validation->add('name', new StringLength(['max'=>20,'min'=> 2,'messageMaximum' => 'Name is maximum 20',
            'messageMinimum' => 'Name is minimum 2']));
        $validation->add('relationship_to_child', new PresenceOf(['message' => 'relationship_to_child is required']));
        $validation->add('amount_of_time_spent_with_child', new PresenceOf(['message' => 'amount_of_time_spent_with_child is required']));
        $messages = $validation->validate($input_data);
        if (count($messages)):
            foreach ($messages as $message) :
                $result[] = $message->getMessage();
            endforeach;
            return $this->response->setJsonContent($result);
        else:
            $collection = new NidaraKidCaregiverInfo();
            $collection->id = $input_data->id;
            $collection->nidara_kid_profile_id = $input_data->nidara_kid_profile_id;
            $collection->name = $input_data->name;
            $collection->relationship_to_child = $input_data->relationship_to_child;
            $collection->amount_of_time_spent_with_child = $input_data->amount_of_time_spent_with_child;
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
     * This function using to NidaraKidCaregiverInfo information edit
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
            $validation->add('name', new PresenceOf(['message' => 'name is required']));
            $validation->add('name', new Alpha(['message'=>'Name is only letters']));
            $validation->add('name', new StringLength(['max'=>20,'min'=> 2,'messageMaximum' => 'Name is maximum 20',
                'messageMinimum' => 'Name is minimum 2']));
            $validation->add('relationship_to_child', new PresenceOf(['message' => 'relationship_to_childis required']));
            $validation->add('amount_of_time_spent_with_child', new PresenceOf(['message' => 'amount_of_time_spent_with_childis required']));
            $messages = $validation->validate($input_data);
            if (count($messages)):
                foreach ($messages as $message):
                    $result[] = $message->getMessage();
                endforeach;
                return $this->response->setJsonContent($result);
            else:
                $collection = NidaraKidCaregiverInfo::findFirstByid($id);
                if ($collection):
                    $collection->id = $input_data->id;
                    $collection->nidara_kid_profile_id = $input_data->nidara_kid_profile_id;
                    $collection->name = $input_data->name;
                    $collection->relationship_to_child = $input_data->relationship_to_child;
                    $collection->amount_of_time_spent_with_child = $input_data->amount_of_time_spent_with_child;
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
            $collection = NidaraKidCaregiverInfo::findFirstByid($id);
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
