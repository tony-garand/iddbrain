<?php

namespace brain\Http\Controllers;

use Illuminate\Http\Request;
use Mockery\Exception;
use Illuminate\Support\Facades\DB;

class ClientNoteController extends Controller
{
    /**
     * Save a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $author_id
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request, $author_id = null)
    {
        $client_id = $request->get('client_id');
        try {
            DB::table('client_notes')->where('client_id', $client_id)->insert([
                'note' => $request->get('note'),
                'manual' => 1,
                'client_id' => $client_id,
                'author_id' => $author_id,
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
            ]);

            $request->session()->flash("status", "Note created successfully!");
        } catch(\Exception $e) {
            $request->session()->flash("status", $e->getMessage());
        }
        return redirect('/clients/view/'.$client_id.'#notes');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $note = DB::table('client_notes')->where('id', $id)->where('manual', true)->first();
            if($note->author_id == auth()->id()) {
                DB::table('client_notes')->where('id', $id)->update([
                    'note' => $request->get('note'),
                    'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
                ]);
            }


        } catch(Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
        return response()->json(['success' => true, 'message' => 'Note has been successfully updated!']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        try {
            $note = DB::table('client_notes')->where('id', $id)->where('manual', true)->where('author_id', auth()->id())->first();
            if($note) {
                DB::table('client_notes')->where('id', $id)->delete();
            } else {
                throw new Exception('Unable to delete note!');
            }
        } catch(Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
        return response()->json(['success' => true, 'message' => 'Client Note successfully deleted!']);
    }
}
