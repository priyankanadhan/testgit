<?php

use Phalcon\Mvc\Micro;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;

class NidarakidschoolinfoController extends \Phalcon\Mvc\Controller {

    public function index() {
        
    }

    /**
     * Fetch all Record from database :-
     */
    public function viewall() {
        $subject = NidaraKidSchoolInfo::find();
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
            $collection = NidaraKidSchoolInfo::findFirstByid($id);
            if ($collection):
                return Json_encode($collection);
            else:
                return $this->response->setJsonContent(['status' => 'Error', 'Message' => 'Data not found']);
            endif;
        endif;
    }

    /**
     * This function using to create NidaraKidSchoolInfo information
     */
    public function create() {

        $input_data = $this->request->getJsonRawBody();

        /**
         * This object using valitaion 
         */
        $validation = new Validation();
        $validation->add('id', new PresenceOf(['message' => 'id is required']));
        $validation->add('nidara_kid_profile_id', new PresenceOf(['message' => 'nidara_kid_profile_id is required']));
        $validation->add('school_name', new PresenceOf(['message' => 'school_name is required']));
        $validation->add('school_type', new PresenceOf(['message' => 'school_type is required']));
        $validation->add('address2', new PresenceOf(['message' => 'address2 is required']));
        $validation->add('town_city', new PresenceOf(['message' => 'town_city is required']));
        $validation->add('state', new PresenceOf(['message' => 'state is required']));
        $validation->add('country', new PresenceOf(['message' => 'country is required']));
        $messages = $validation->validate($input_data);
        if (count($messages)):
            foreach ($messages as $message) :
                $result[] = $message->getMessage();
            endforeach;
			return $this->response->setJsonContent ( $result );
		 else :
			$kidschoolinfoexist = NidaraKidSchoolInfo::findFirstBynidara_kid_profile_id ( $input_data->nidara_kid_profile_id );
			if (! empty ( $kidschoolinfoexist )) {
				return $this->response->setJsonContent ( [ 
						'status' => false,
						'message' => 'School information already exist for this kid' 
				] );
			}
	    	$kidschoolinfo = new NidaraKidSchoolInfo ();
	    	$kidschoolinfo->id = $input_data->id;
	   		$kidschoolinfo->nidara_kid_profile_id = $input_data->nidara_kid_profile_id;
            $kidschoolinfo->school_name = $input_data->school_name;
            $kidschoolinfo->school_type = $input_data->school_type;
            $kidschoolinfo->address2 = $input_data->address2;
            $kidschoolinfo->town_city = $input_data->town_city;
            $kidschoolinfo->state = $input_data->state;
            $kidschoolinfo->country = $input_data->country;
            $kidschoolinfo->created_at =date('Y-m-d H:i:s');
            $kidschoolinfo->created_by = 1;
            $kidschoolinfo->modified_at = date('Y-m-d H:i:s');
            if ($collection->save()):
                return $this->response->setJsonContent(['status' => 'Ok', 'message' => 'succefully']);
            else:
                return $this->response->setJsonContent(['status' => 'Error', 'message' => 'Failed']);
            endif;
        endif;
    }

    /**
     * This function using to NidaraKidSchoolInfo information edit
     */
    public function update() {

        $input_data = $this->request->getJsonRawBody();
        $id = isset($input_data->id) ? $input_data->id : '';
        if (empty($id)):
            return $this->response->setJsonContent(['status' => 'Error', 'message' => 'Id is null']);
        else:
            $validation = new Validation();
            $validation->add('id', new PresenceOf(['message' => 'idis required']));
            $validation->add('nidara_kid_profile_id', new PresenceOf(['message' => 'nidara_kid_profile_idis required']));
            $validation->add('school_name', new PresenceOf(['message' => 'school_nameis required']));
            $validation->add('school_type', new PresenceOf(['message' => 'school_typeis required']));
            $validation->add('address2', new PresenceOf(['message' => 'address2is required']));
            $validation->add('town_city', new PresenceOf(['message' => 'town_cityis required']));
            $validation->add('state', new PresenceOf(['message' => 'stateis required']));
            $validation->add('country', new PresenceOf(['message' => 'countryis required']));
            $messages = $validation->validate($input_data);
            if (count($messages)):
                foreach ($messages as $message):
                    $result[] = $message->getMessage();
                endforeach;
                return $this->response->setJsonContent($result);
            else:
                $collection = NidaraKidSchoolInfo::findFirstByid($id);
                if ($collection):
                    $collection->id = $input_data->id;
                    $collection->nidara_kid_profile_id = $input_data->nidara_kid_profile_id;
                    $collection->school_name = $input_data->school_name;
                    $collection->school_type = $input_data->school_type;
                    $collection->address2 = $input_data->address2;
                    $collection->town_city = $input_data->town_city;
                    $collection->state = $input_data->state;
                    $collection->country = $input_data->country;
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
            $collection = NidaraKidSchoolInfo::findFirstByid($id);
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
