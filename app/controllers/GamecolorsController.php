<?php
                    use Phalcon\Mvc\Micro;
                    use Phalcon\Validation;
                    use Phalcon\Validation\Validator\PresenceOf;
                    
                    class GamecolorsController extends \Phalcon\Mvc\Controller {
                        public function index() {        
                        }/**
                        * Fetch all Record from database :-
                        */
                       public function viewall() {
                           $game_colors = GameColors::find();
                           if ($game_colors):
                               return Json_encode($game_colors);
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
                               $game_colors = GameColors::findFirstByid($id);
                               if ($game_colors):
                                   return Json_encode($game_colors);
                               else:
                                   return $this->response->setJsonContent(['status' => false, 'Message' => 'Data not found']);
                               endif;
                           endif;
                       }
/**
                        * This function using to create GameColors information
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
$validation->add('day', new PresenceOf(['message' => 'day is required']));
$validation->add('background_color', new PresenceOf(['message' => 'background color is required']));
$messages = $validation->validate($input_data);
                        if (count($messages)):
                            foreach ($messages as $message) :
                                $result[] = $message->getMessage();
                            endforeach;
                            return $this->response->setJsonContent($result);
                        else:
                            $game_colors = new GameColors();$game_colors->id = $input_data->id;
$game_colors->day = $input_data->day;
$game_colors->background_color = $input_data->background_color;
  if ($game_colors->save()):
                                    return $this->response->setJsonContent(['status' => true, 'message' => 'successfully']);
                                else:
                                    return $this->response->setJsonContent(['status' => false, 'message' => 'Failed']);
                                endif;
                            endif;
                        } 
/**
                        * This function using to GameColors information edit
                        */
                       public function update($id = null) {

                           $input_data = $this->request->getJsonRawBody();
                           $id = isset($input_data->id) ? $input_data->id : '';
                           if (empty($id)):
                               return $this->response->setJsonContent(['status' => false, 'message' => 'Id is null']);
                           else:
                               $validation = new Validation();

$validation->add('day', new PresenceOf(['message' => 'day is required']));
$validation->add('background_color', new PresenceOf(['message' => 'background color is required']));
$messages = $validation->validate($input_data);
                        if (count($messages)):
                            foreach ($messages as $message):
                                $result[] = $message->getMessage();
                            endforeach;
                            return $this->response->setJsonContent($result);
                        else:
                            $game_colors = GameColors::findFirstByid($id);
                            if ($game_colors):
$game_colors->id = $input_data->id;
$game_colors->day = $input_data->day;
$game_colors->background_color = $input_data->background_color;
 if ($game_colors->save()):
                                            return $this->response->setJsonContent(['status' => true, 'message' => 'successfully']);
                                        else:
                                            return $this->response->setJsonContent(['status' => false, 'message' => 'Failed']);
                                        endif;
                                    else:
                                        return $this->response->setJsonContent(['status' => false, 'message' => 'Invalid id']);
                                    endif;
                                endif;
                            endif;
                        }/**
                        * This function using delete kids caregiver information
                        */
                       public function delete() {

                           $input_data = $this->request->getJsonRawBody();
                           $id = isset($input_data->id) ? $input_data->id : '';
                           if (empty($id)):
                               return $this->response->setJsonContent(['status' => false, 'message' => 'Id is null']);
                           else:
                               $game_colors = GameColors::findFirstByid($id);
                               if ($game_colors):
                                   if ($game_colors->delete()):
                                       return $this->response->setJsonContent(['status' => true, 'Message' => 'Record has been deleted successfully ']);
                                   else:
                                       return $this->response->setJsonContent(['status' => false, 'Message' => 'Data could not be deleted']);
                                   endif;
                               else:
                                   return $this->response->setJsonContent(['status' => false, 'Message' => 'ID doesn\'t']);
                               endif;
                           endif;
                       }

                   }
