<?php
use Phalcon\Mvc\Micro;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
class DailyschedulerdaysmapController extends \Phalcon\Mvc\Controller {
	public function index() {
	}
	
	/**
	 * Fetch all Record from database :-
	 */
	public function viewall() {
		$daily_scheduler = DailySchedulerDaysMap::find ();
		if ($daily_scheduler) :
			return Json_encode ( $daily_scheduler );
		 else :
			return $this->response->setJsonContent ( [ 
					'status' => false,
					'Message' => 'Failed' 
			] );
		endif;
	}
	/**
	 * This function using to create DailySchedulerDaysMap information
	 */
	public function create() {
		$input_data = $this->request->getJsonRawBody ();
		$scheduler_days = new DailyScheduler ();
		$scheduler_days->id = $input_data->id;
		$scheduler_days->time = $input_data->time;
		$scheduler_days->reminder = $input_data->reminder;
		$scheduler_days->nidara_kid_profile_id = $input_data->nidara_kid_profile_id;
		$scheduler_days->save ();
		
		/**
		 * This object using valitaion
		 */
		$validation = new Validation ();
		$validation->add ( 'time', new PresenceOf ( [ 
				'message' => 'time is required' 
		] ) );
		$validation->add ( 'reminder', new PresenceOf ( [ 
				'message' => 'reminder is required' 
		] ) );
		$validation->add ( 'scheduler_days_id', new PresenceOf ( [ 
				'message' => 'SchedulerDaysId is required' 
		] ) );
		
		$messages = $validation->validate ( $input_data );
		if (count ( $messages )) :
			foreach ( $messages as $message ) :
				$result [] = $message->getMessage ();
			endforeach
			;
			return $this->response->setJsonContent ( $result );
		 else :
			$i = 1;
			$scheduler = $input_data->scheduler_days_id;
			foreach ( $scheduler as $key => $value ) {
				$daily_scheduler = new DailySchedulerDaysMap ();
				$daily_scheduler->id = $i;
				$daily_scheduler->daily_scheduler_id = $input_data->id;
				$daily_scheduler->scheduler_days_id = $value;
				if (! $daily_scheduler->save ()) {
					return $this->response->setJsonContent ( [ 
							'status' => false,
							'message' => 'Failed' 
					] );
				}
				$i ++;
			}
			return $this->response->setJsonContent ( [ 
					'status' => true,
					'message' => 'successfully' 
			] );
		

		endif;
	}
}

