<?php

use Phalcon\Mvc\Micro;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;

class StandardindicatorsmapController extends \Phalcon\Mvc\Controller {

    public function index() {
        
    }

    /**
     * Fetch all Record from database :-
     */
    public function viewall() {
        $subject = StandardIndicatorsMap::find();
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
            $collection = StandardIndicatorsMap::findFirstByid($id);
            if ($collection):
                return Json_encode($collection);
            else:
                return $this->response->setJsonContent(['status' => 'Error', 'Message' => 'Data not found']);
            endif;
        endif;
    }

    /**
     * This function using to create StandardIndicatorsMap information
     */
    public function create() {

        $input_data = $this->request->getJsonRawBody();

        /**
         * This object using valitaion 
         */
        $validation = new Validation();
        $validation->add('id', new PresenceOf(['message' => 'id is required']));
        $validation->add('standard_id', new PresenceOf(['message' => 'standard_id is required']));
        $validation->add('indicators_id', new PresenceOf(['message' => 'indicators_id is required']));
        $messages = $validation->validate($input_data);
        if (count($messages)):
            foreach ($messages as $message) :
                $result[] = $message->getMessage();
            endforeach;
            return $this->response->setJsonContent($result);
        else:
            $collection = new StandardIndicatorsMap();
            $collection->id = $input_data->id;
            $collection->standard_id = $input_data->standard_id;
            $collection->indicators_id = $input_data->indicators_id;
            if ($collection->save()):
                return $this->response->setJsonContent(['status' => 'Ok', 'message' => 'succefully']);
            else:
                return $this->response->setJsonContent(['status' => 'Error', 'message' => 'Failed']);
            endif;
        endif;
    }

    /**
     * This function using to StandardIndicatorsMap information edit
     */
    public function update($id = null) {

        $input_data = $this->request->getJsonRawBody();
        $id = isset($input_data->id) ? $input_data->id : '';
        if (empty($id)):
            return $this->response->setJsonContent(['status' => 'Error', 'message' => 'Id is null']);
        else:
            $validation = new Validation();
            $validation->add('id', new PresenceOf(['message' => 'idis required']));
            $validation->add('standard_id', new PresenceOf(['message' => 'standard_idis required']));
            $validation->add('indicators_id', new PresenceOf(['message' => 'indicators_idis required']));
            $messages = $validation->validate($input_data);
            if (count($messages)):
                foreach ($messages as $message):
                    $result[] = $message->getMessage();
                endforeach;
                return $this->response->setJsonContent($result);
            else:
                $collection = StandardIndicatorsMap::findFirstByid($id);
                if ($collection):
                    $collection->id = $input_data->id;
                    $collection->standard_id = $input_data->standard_id;
                    $collection->indicators_id = $input_data->indicators_id;
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
            $collection = StandardIndicatorsMap::findFirstByid($id);
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
