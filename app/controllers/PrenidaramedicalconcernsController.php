<?php

use Phalcon\Mvc\Micro;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;

class PrenidaramedicalconcernsController extends \Phalcon\Mvc\Controller {

    public function index() {
        
    }

/**
     * Fetch all Record from database :-
     */

    public function viewall() {
        $medical = PreNidaraMedicalConcerns::find();
        if ($medical):
            return Json_encode($medical);
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
            $medical = PreNidaraMedicalConcerns::findFirstByid($id);
            if ($medical):
                return Json_encode($medical);
            else:
                return $this->response->setJsonContent(['status' => 'Error', 'Message' => 'Data not found']);
            endif;
        endif;
    }

    /**
     * This function using to create PreNidaraMedicalConcerns information
     */
    public function create() {

        $input_data = $this->request->getJsonRawBody();
        if (empty($input_data)) {
            return $this->response->setJsonContent(["status" => false, "message" => "Please give the input datas"]);
        }


        /**
         * This object using valitaion 
         */
        $validation = new Validation();
        $validation->add('medical_concern', new PresenceOf(['message' => 'medical_concern is required']));
        $validation->add('status', new PresenceOf(['message' => 'status is required']));

        $validation->add('nidara_kid_profile_id', new PresenceOf(['message' => 'nidara_kid_profile_id is required']));
        $messages = $validation->validate($input_data);
        if (count($messages)):
            foreach ($messages as $message) :
                $result[] = $message->getMessage();
            endforeach;
            return $this->response->setJsonContent($result);
        else:
            $medical = new PreNidaraMedicalConcerns();
            $medical->id = $input_data->id;
            $medical->medical_concern = $input_data->medical_concern;
            $medical->status = $input_data->status;
            $medical->created_at = date('Y-m-d H:i:s');
            $medical->created_by = $input_data->id;
            $medical->modified_at = date('Y-m-d H:i:s');
            $medical->nidara_kid_profile_id = $input_data->nidara_kid_profile_id;
            if ($medical->save()):
                return $this->response->setJsonContent(['status' => 'Ok', 'message' => 'succefully']);
            else:
                return $this->response->setJsonContent(['status' => 'Error', 'message' => 'Failed']);
            endif;
        endif;
    }

    /**
     * This function using to PreNidaraMedicalConcerns information edit
     */
    public function update($id = null) {

        $input_data = $this->request->getJsonRawBody();
        $id = isset($input_data->id) ? $input_data->id : '';
        if (empty($id)):
            return $this->response->setJsonContent(['status' => 'Error', 'message' => 'Id is null']);
        else:
            $validation = new Validation();

            $validation->add('medical_concern', new PresenceOf(['message' => 'medical_concernis required']));
            $validation->add('status', new PresenceOf(['message' => 'statusis required']));

            $validation->add('nidara_kid_profile_id', new PresenceOf(['message' => 'nidara_kid_profile_idis required']));
            $messages = $validation->validate($input_data);
            if (count($messages)):
                foreach ($messages as $message):
                    $result[] = $message->getMessage();
                endforeach;
                return $this->response->setJsonContent($result);
            else:
                $medical = PreNidaraMedicalConcerns::findFirstByid($id);
                if ($medical):
                    $medical->id = $input_data->id;
                    $medical->medical_concern = $input_data->medical_concern;
                    $medical->status = $input_data->status;

                    $medical->created_by = $input_data->id;
                    $medical->modified_at = date('Y-m-d H:i:s');
                    $medical->nidara_kid_profile_id = $input_data->nidara_kid_profile_id;
                    if ($medical->save()):
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
            $medical = PreNidaraMedicalConcerns::findFirstByid($id);
            if ($medical):
                if ($medical->delete()):
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
