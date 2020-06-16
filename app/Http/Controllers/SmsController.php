<?php

namespace brain\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Twilio\Rest\Client;
use Twilio\Twiml;
use Twilio\Exceptions\RestException;

class SmsController extends Controller {

	public function __construct() {
	}

	public function convo_hello(Request $request) {

		if ($request->get('format') == "json") {

			$data = [];

			if ( ( ( $request->get('convo_id') ) || ( $request->get('uuid') ) ) && ( $request->get('phone') ) ) {

				$client = new Client(env('SMS_SID'), env('SMS_TOKEN'));
				try {
					$phone_number_check = $client->lookups->v1->phoneNumbers(trim($request->get('phone')))->fetch(array("countryCode" => "US"));

					// get the sms_convo data..
					if ($request->get('uuid')) {
						$convo = DB::table('sms_convos')->where('uuid', $request->get('uuid'))->first();
					} else {
						$convo = DB::table('sms_convos')->where('id', $request->get('convo_id'))->first();
					}
					if ($convo) {
						$ms = [];
						if ($convo->all_locations == 1) {
							// get this clients default mg..
							$ms = DB::table('messaging_services')->where('id', $convo->client_id)->first();
						}
						if ($ms) {
							$msg_service = $ms->sid;
							// we should return the 'welcome' message with the trigger code included.
							$reply = $convo->welcome . " - To continue, reply with " . strtoupper($convo->trigger);
							$resp = $client->messages->create(
								$phone_number_check->phoneNumber,
								array(
									'messagingServiceSid' => $msg_service,
									'body' => trim($reply)
								)
							);
						}
					}

					$data['status'] = 'OK';
					$data['detail'] = '';

				} catch (RestException $e) {
					$data['status'] = 'NOT-OK';
					$data['detail'] = 'Invalid phone number format';
				}

			} else {
				$data['status'] = 'NOT-OK';
				$data['detail'] = 'Missing UUID / Phone';
			}

			$json = json_encode($data);
			echo $json;
			exit;

		} else {
			if ( ( ( $request->get('convo_id') ) || ( $request->get('uuid') ) ) && ( $request->get('phone') ) ) {

				$client = new Client(env('SMS_SID'), env('SMS_TOKEN'));
				try {
					$phone_number_check = $client->lookups->v1->phoneNumbers(trim($request->get('phone')))->fetch(array("countryCode" => "US"));

					// get the sms_convo data..
					if ($request->get('uuid')) {
						$convo = DB::table('sms_convos')->where('uuid', $request->get('uuid'))->first();
					} else {
						$convo = DB::table('sms_convos')->where('id', $request->get('convo_id'))->first();
					}
					if ($convo) {
						$ms = [];
						if ($convo->all_locations == 1) {
							// get this clients default mg..
							$ms = DB::table('messaging_services')->where('id', $convo->client_id)->first();
						}
						if ($ms) {
							$msg_service = $ms->sid;
							// we should return the 'welcome' message with the trigger code included.
							$reply = $convo->welcome . " - To continue, reply with " . strtoupper($convo->trigger);
							$resp = $client->messages->create(
								$phone_number_check->phoneNumber,
								array(
									'messagingServiceSid' => $msg_service,
									'body' => trim($reply)
								)
							);
						}
					}
				} catch (RestException $e) {
					return redirect($request->get('redirect'));
				}

			}

			return redirect($request->get('redirect'));
			exit;

		}

	}

	public function incoming_sms(Request $request) {

		$userInput = $request->all();
		foreach ($userInput as $k=>$v) {
			Log::info('incoming_sms ... ' . $k . ' -> ' . $v);
		}

		$raw_incoming_message = trim($request->Body);
		$incoming_message = strtolower(trim($request->Body));
		$messaging_service_sid = $request->MessagingServiceSid;
		$phone_from = $request->From;
		$phone_to = $request->To;
		$sms_message_sid = $request->SmsMessageSid;
		$message_sid = $request->MessageSid;
		$event_done = 0;

		$clean_phone = str_replace("+1", "", $phone_from);
		$clean_phone = str_replace("+", "", $clean_phone);
		$clean_phone_to = str_replace("+1", "", $phone_to);
		$clean_phone_to = str_replace("+", "", $clean_phone_to);

		// get $client_id - if we dont have one, skip processing as we dont know what they are trying to do..
		$ms_check = DB::table('messaging_services')->where('sid', $messaging_service_sid)->first();
		if (@$ms_check->client_id) {

			// create or find the sms_data record for this from user; this is the data we will use for smart filler.
			$sms_data_check = DB::table('sms_data')->where('client_id', $ms_check->client_id)->where('from', $clean_phone)->first();
			if ($sms_data_check) {
				$sms_data = $sms_data_check;
			} else {
				$sms_data_id = DB::table('sms_data')->insertGetId(
					[
						'client_id' => $ms_check->client_id,
						'status' => 1,
						'from' => $clean_phone,
						'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
						'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
					]
				);
				$sms_data = DB::table('sms_data')->where('id', $sms_data_id)->first();
			}

			Log::info('incoming_sms ... sms_data id = ' . $sms_data->id);

			// unsubscribe ////////////////////////////////////////////////////////
			if ( ($incoming_message == "unsubscribe") || ($incoming_message == "remove") || ($incoming_message == "stop") ) {

				Log::info('incoming_sms ... unsubscribe = ' . $clean_phone);

				DB::table('sms_data')
					->where('from', $clean_phone)
					->where('client_id', $ms_check->client_id)
					->update([
						'status' => 0,
						'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
					]
				);

				$event_done = 1;

			}

			// restart (resubscribe) //////////////////////////////////////////////
			if ( ($incoming_message == "unstop") || ($incoming_message == "start") ) {

				Log::info('incoming_sms ... start = ' . $clean_phone);

				DB::table('sms_data')
					->where('from', $clean_phone)
					->where('client_id', $ms_check->client_id)
					->update([
						'status' => 1,
						'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
					]
				);

				$event_done = 1;

			}

			// sms conversations //////////////////////////////////////////////////
			$my_thread_id = $request->cookie('thread_id');
			$my_step = $request->cookie('step');
			$my_prev_step = $request->cookie('prev_step');

			Log::info('----- cookie-check ... $my_thread_id = ' . $my_thread_id . ' .. $my_step = ' . $my_step . ' .. $my_prev_step = ' . $my_prev_step);

			if ($my_thread_id) {

				// we have a thread; can we find the next step?
				$thread_info = DB::table('sms_convo_threads')->where('id', $my_thread_id)->first();
				if ($thread_info) {

					if ($my_step) {
						// try to get the next step..
						$script_info = DB::table('sms_convo_scripts')->where('sms_convo_id', $thread_info->sms_convo_id)->where('step', '>=', $my_step)->first();
					} else {
						// get step 1..
						$script_info = DB::table('sms_convo_scripts')->where('sms_convo_id', $thread_info->sms_convo_id)->where('step', '>=', 1)->first();
					}

					if ($script_info) {

						$my_thread_reply_id = DB::table('sms_convo_thread_replies')->insertGetId(
							[
								'sms_convo_thread_id' => $my_thread_id,
								'sms_convo_script_id' => $script_info->id,
								'reply_body' => $incoming_message,
								'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
								'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
							]
						);

						// do we have an actionable item for this?
						$last_script_info = DB::table('sms_convo_scripts')->where('sms_convo_id', $thread_info->sms_convo_id)->where('step', $my_prev_step)->first();
						if ($last_script_info) {
							if ($last_script_info->data_destination) {
								Log::info('data_destination TRIGGER ... ' . $last_script_info->data_destination . ' -> ' . $incoming_message);

								$destination_break = explode("-", $last_script_info->data_destination);

								if ($destination_break[0] == "sms_data") {

									if ($destination_break[1] == "name") {
										// can we split this name on a space? if so, we need to update both fields, if not, we just need to update first name..
										$_name_parts = explode(' ', $raw_incoming_message, 2);
										if (count($_name_parts) > 1) {
											DB::table('sms_data')
												->where('client_id', $ms_check->client_id)
												->where('from', $thread_info->from)
												->update([
														'first_name' => $_name_parts[0],
														'last_name' => $_name_parts[1],
														'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
													]
											);
										} else {
											DB::table('sms_data')
												->where('client_id', $ms_check->client_id)
												->where('from', $thread_info->from)
												->update([
														'first_name' => $_name_parts[0],
														'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
													]
											);
										}

									} else {
										DB::table('sms_data')
											->where('client_id', $ms_check->client_id)
											->where('from', $thread_info->from)
											->update([
													$destination_break[1] => $raw_incoming_message,
													'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
												]
										);
									}

								}

//								if ($destination_break[0] == "subscribers") {
//									DB::table('subscribers')
//										->where('phone', 'like', '%'.$thread_info->from.'%')
//										->update([
//												$destination_break[1] => $raw_incoming_message,
//												'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
//											]
//									);
//								}
//
//								if ($destination_break[0] == "subscribers_catch") {
//									DB::table('subscribers_catch')
//										->where('phone_from', 'like', '%'.$thread_info->from.'%')
//										->update([
//												$destination_break[1] => $raw_incoming_message,
//												'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
//											]
//									);
//								}

							}
						}

						$_subscriber = DB::table('sms_data')->where('client_id', $ms_check->client_id)->where('from', $clean_phone)->first();
						$_client = DB::table('clients')->where('id', $ms_check->client_id)->first();
						$_subscriber_catch = array();

						$message = $this->_magic_tags($script_info->script_body, $_client, $_subscriber, $_subscriber_catch);

						$response = new Twiml;
						$response->message($message);

						// do we have another step? if so, keep the cookies.. otherwise destroy them
						$next_script_info = DB::table('sms_convo_scripts')->where('sms_convo_id', $thread_info->sms_convo_id)->where('step', '>=', ($script_info->step+1))->first();
						if ($next_script_info) {
							return response($response)->cookie('thread_id', $my_thread_id)->cookie('step', $next_script_info->step)->cookie('prev_step', $script_info->step);
						} else {
							return response($response)->cookie('thread_id', '')->cookie('step', '')->cookie('prev_step', '');
						}

					}

				} else {

					// we cant find this thread; its possible it was destroyed; unset the cookies so we can reset..
					Log::info('ERR: cant find this thread_id, aborting..');
					return response('')->cookie('thread_id', '')->cookie('step', '')->cookie('prev_step', '');

				}

			} else {

				if ($event_done == 0) {
					// we still dont have an action, we should check if this is a trigger word from the DB (scripts).. (global)
					Log::info('event_done = 0, checking GLOBAL convos ... incoming_message = [' . $incoming_message . ']');
					$global_convo_check = DB::table('sms_convos')->where('trigger', $incoming_message)->where('all_locations', 1)->first();

					if ($global_convo_check) {
						$ms_info = DB::table('messaging_services')->where('sid', $messaging_service_sid)->first();
						if ($ms_info) {

							$client_info = DB::table('clients')->where('id', $ms_check->client_id)->first();
							if ($client_info) {

								list ($response, $thread_info, $script_info) = $this->_handle_convo_event($global_convo_check->id, $request->cookie('thread_id'), $request->cookie('step'), $messaging_service_sid, $sms_message_sid, $clean_phone, $clean_phone_to, $client_info);

								// do we have another step? if so, keep the cookies.. otherwise destroy them
								$next_script_info = DB::table('sms_convo_scripts')->where('sms_convo_id', $thread_info->sms_convo_id)->where('step', '>=', ($script_info->step+1))->first();
								if ($next_script_info) {
									return response($response)->cookie('thread_id', $thread_info->id)->cookie('step', $next_script_info->step)->cookie('prev_step', $script_info->step);
								} else {
									return response($response)->cookie('thread_id', '')->cookie('step', '')->cookie('prev_step', '');
								}

								$event_done = 1;
							}
						}
					}
				}

				if ($event_done == 0) {
					// we still dont have an action, we should check if this is a trigger word from the DB (scripts).. (local)
					Log::info('event_done = 0, checking LOCAL convos ... messaging_service_sid = ' . $messaging_service_sid . ' ... incoming_message = [' . $incoming_message . ']');
					$ms_info = DB::table('messaging_services')->where('sid', $messaging_service_sid)->first();
					if ($ms_info) {

						$client_info = DB::table('clients')->where('id', $ms_check->client_id)->first();
						if ($client_info) {

							$local_convo_check = DB::table('sms_convo_messaging_services')->join('sms_convos', 'sms_convos.id', '=', 'sms_convo_messaging_services.sms_convo_id')->where('sms_convos.trigger', $incoming_message)->where('sms_convo_messaging_services.messaging_service_id', $ms_info->id)->first();
							if ($local_convo_check) {
								list ($response, $thread_info, $script_info) = $this->_handle_convo_event($local_convo_check->sms_convo_id, $request->cookie('thread_id'), $request->cookie('step'), $messaging_service_sid, $sms_message_sid, $clean_phone, $clean_phone_to, $client_info);

								// do we have another step? if so, keep the cookies.. otherwise destroy them
								if ( ($thread_info) && ($script_info) ) {
									$next_script_info = DB::table('sms_convo_scripts')->where('sms_convo_id', $thread_info->sms_convo_id)->where('step', '>=', ($script_info->step+1))->first();
									if ($next_script_info) {
										Log::info('local:next_step=1');
										return response($response)->cookie('thread_id', $thread_info->id)->cookie('step', $next_script_info->step)->cookie('prev_step', $script_info->step);
									} else {
										Log::info('local:next_step=0');
										return response($response)->cookie('thread_id', '')->cookie('step', '')->cookie('prev_step', '');
									}
								} else {
									Log::info('local:next_step=00');
									return response($response)->cookie('thread_id', '')->cookie('step', '')->cookie('prev_step', '');
								}

								$event_done = 1;
							}
						}


					}
				}

			}

		} else {
			Log::info('incoming_sms ... no matching ms_check -> client_id');
		}
		exit;

	}

	private function _magic_tags($body, $_client, $_subscriber, $_subscriber_catch) {
		$message = $body;
		if ($_client) {
			$message = str_replace("[[LocationName]]", $_client->name, $message);
		} else {
			$message = str_replace("[[LocationName]]", "", $message);
		}
		if ($_subscriber) {
			$message = str_replace("[[FullName]]", ((trim($_subscriber->last_name)) ? $_subscriber->first_name . " " . $_subscriber->last_name : $_subscriber->first_name), $message);
			$message = str_replace("[[FirstName]]", $_subscriber->first_name, $message);
			$message = str_replace("[[Email]]", $_subscriber->email, $message);
		} else {
			$message = str_replace("[[FullName]]", "", $message);
			$message = str_replace("[[FirstName]]", "", $message);
			$message = str_replace("[[Email]]", "", $message);
		}
		return $message;
	}

	private function _handle_convo_event($convo_id, $my_thread_id, $my_step, $messaging_service_sid, $sms_message_sid, $clean_phone, $clean_phone_to, $client_info) {

		Log::info('FIRE: _handle_convo_event(' . $convo_id . ',' . $my_thread_id . ',' . $my_step . ',' . $messaging_service_sid . ',' . $sms_message_sid . ',' . $clean_phone . ',' . $clean_phone_to . ',' . 'array()' . ')');

		$has_thread = 0;

		if ($my_thread_id) {
			// check thread
			$thread_info = DB::table('sms_convo_threads')->where('id', $my_thread_id)->first();
			if ($thread_info) {
				$has_thread = 1;
			}
		}

		if ($has_thread == 0) {
			// create new thread
			$my_thread_id = DB::table('sms_convo_threads')->insertGetId(
				[
					'sms_convo_id' => $convo_id,
					'messaging_service_sid' => $messaging_service_sid,
					'sms_message_sid' => $sms_message_sid,
					'from' => $clean_phone,
					'to' => $clean_phone_to,
					'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
					'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
				]
			);
			$thread_info = DB::table('sms_convo_threads')->where('id', $my_thread_id)->first();
		}

		if ($my_step) {
			// try to get the next step..
			$script_info = DB::table('sms_convo_scripts')->where('sms_convo_id', $thread_info->sms_convo_id)->where('step', '>=', $my_step)->first();
		} else {
			// get step 1..
			$script_info = DB::table('sms_convo_scripts')->where('sms_convo_id', $thread_info->sms_convo_id)->where('step', '>=', 1)->first();
		}

		if ($script_info) {
			$_subscriber = DB::table('sms_data')->where('client_id', $client_info->id)->where('from', $clean_phone)->first();
			$_client = $client_info;
			$_subscriber_catch = array();

			$message = $this->_magic_tags($script_info->script_body, $_client, $_subscriber, $_subscriber_catch);
			Log::info('$message = ' . $message);

			$response = new Twiml;
			$response->message($message);

			return array($response, $thread_info, $script_info);
		} else {
			Log::info('$script_info = false');
		}

	}


}
