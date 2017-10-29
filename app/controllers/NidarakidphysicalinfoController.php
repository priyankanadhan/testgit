<?php

use Phalcon\Mvc\Micro;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Digit;
use Phalcon\Validation\Validator\Alpha;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\PresenceOf;

class NidarakidphysicalinfoController extends \Phalcon\Mvc\Controller {

    public function index() {
        
    }

    /**
     * Fetch all Record from database :-
     */
    public function viewall() {
        $subject = NidaraKidPhysicalInfo::find();
        if ($subject):
            return Json_encode($subject);
        else:
            return $this->response->setJsonContent(['status' => 'Error', 'Message' => 'Faield']);
        endif;
    }

    /*
     * Fetch Record from database based on ID :-
     */

    public function getbyid(){

        $input_data = $this->request->getJsonRawBody();
        $id = isset($input_data->id) ? $input_data->id : '';
        if (empty($id)):
            return $this->response->setJsonContent(['status' => 'Error','message' => 'Invalid input parameter']);
        else:
            $collection = NidaraKidPhysicalInfo::findFirstByid($id);
            if ($collection):
                return Json_encode($collection);
            else:
                return $this->response->setJsonContent(['status' => 'Error', 'Message' => 'Data not found']);
            endif;
        endif;
    }

    /**
     * This function using to create NidaraKidPhysicalInfo information
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
        $validation->add('height', new PresenceOf(['message' => 'height is required']));
        $validation->add('height', new Digit(['message'=>'height is only Digit']));
        $validation->add('weight', new PresenceOf(['message' => 'weight is required']));
        $validation->add('weight', new Digit(['message'=>'weight is only Digit']));
        $messages = $validation->validate($input_data);
        if (count($messages)):
            foreach ($messages as $message) :
                $result[] = $message->getMessage();
            endforeach;
            return $this->response->setJsonContent($result);
        else:
            $collection = new NidaraKidPhysicalInfo();
            $collection->id = $input_data->id;
            $collection->nidara_kid_profile_id = $input_data->nidara_kid_profile_id;
            $collection->height = $input_data->height;
            $collection->weight = $input_data->weight;
            $collection->created_at =date('Y-m-d H:i:s');
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
     * This function using to NidaraKidPhysicalInfo information edit
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
            $validation->add('height', new PresenceOf(['message' => 'height is required']));
            $validation->add('height', new Digit(['message'=>'height is only Digit']));
            $validation->add('weight', new PresenceOf(['message' => 'weight is required']));
            $validation->add('weight', new Digit(['message'=>'weight is only Digit']));
            $messages = $validation->validate($input_data);
            if (count($messages)):
                foreach ($messages as $message):
                    $result[] = $message->getMessage();
                endforeach;
                return $this->response->setJsonContent($result);
            else:
                $collection = NidaraKidPhysicalInfo::findFirstByid($id);
                if ($collection):
                    $collection->nidara_kid_profile_id = $input_data->nidara_kid_profile_id;
                    $collection->height = $input_data->height;
                    $collection->weight = $input_data->weight;
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
            $collection = NidaraKidPhysicalInfo::findFirstByid($id);
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
