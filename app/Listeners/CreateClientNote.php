<?php

namespace brain\Listeners;

use brain\Events\NewClientActivity;
//use Illuminate\Queue\InteractsWithQueue;
//use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;
use Illuminate\Support\Facades\DB;

class CreateClientNote
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewClientActivity  $event
     * @return void
     */
    public function handle(NewClientActivity $event)
    {
        $user = auth()->user();
        try {
            $success = DB::table('client_notes')->insert([
                'note' => create_user_link($user) . $event->note,
                'manual' => 0,
                'client_id' => $event->client_id,
                'author_id' => $user->id,
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
            ]);
            if(!$success) {
                throw new Exception('Unable to insert new client note');
            }
        } catch(Exception $e) {
            Log::error('CreateClientNote Listener Error: '.$e->getMessage());
        }
    }
}
