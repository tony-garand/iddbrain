<?php

namespace brain\Http\Controllers;

use Illuminate\Http\Request;
use Mockery\Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use brain\Events\NewClientActivity;

class ClientContactController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function index() {
        $data['contacts'] = DB::table('client_contacts')->orderBy('title')->get();
        return view('tools.client_contacts.index', $data);
    }

    public function view($id) {
        $data['contacts'] = DB::table('client_contacts')->where('id', $id)->first();
        return view('tools.client_contacts.view', $data);
    }

    public function save(Request $request) {
        $client_id = $request->get('client_id');
        try {
            $this->validate($request, [
                'name' => 'required|unique:client_contacts,name',
                'contact_type' => 'required'
            ]);

            DB::table('client_contacts')->insert([
                'contact_type' => $request->get('contact_type'),
                'name' => $request->get('name'),
                'occupation' => $request->get('occupation'),
                'mobile_phone' => $request->get('mobile_phone'),
                'work_phone' => $request->get('work_phone'),
                'fax' => $request->get('fax'),
                'email' => $request->get('email'),
                'comments' => $request->get('comments'),
                'client_id' => $client_id,
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
            ]);

            event(new NewClientActivity($client_id, ' has added a new contact (<a href="/clients/view/'.$client_id.'#contacts">'.$request->get('name').'</a>) for this client.'));

            $request->session()->flash("status", "Client Contact created successfully!");
            return redirect('/clients/view/'.$client_id.'#contacts');
        } catch(Exception $e) {
            $request->session()->flash("status", $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function update(Request $request, $id) {
        $client_id = $request->get('client_id');
        try {
            $this->validate($request, [
                'name' => 'required|unique:client_contacts,name,'.$id,
                'contact_type' => 'required'
            ]);

            DB::table('client_contacts')->where('id', $id)->update([
                'contact_type' => $request->get('contact_type'),
                'name' => $request->get('name'),
                'occupation' => $request->get('occupation'),
                'mobile_phone' => $request->get('mobile_phone'),
                'work_phone' => $request->get('work_phone'),
                'fax' => $request->get('fax'),
                'email' => $request->get('email'),
                'comments' => $request->get('comments'),
                'client_id' => $client_id,
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
            ]);

            event(new NewClientActivity($client_id, ' has updated the contact (<a href="/clients/view/'.$client_id.'#contacts">'.$request->get('name').'</a>) for this client.'));

            $request->session()->flash("status", "Client Contact updated successfully!");
            return redirect('/clients/view/'.$client_id.'#contacts');
        } catch(Exception $e) {
            $request->session()->flash("status", $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function delete(Request $request, $id) {
        try {
            $client_id = $request->get('client_id');
            $contact = DB::table('client_contacts')->where('id', $id)->first();
            if($contact) {
                DB::table('client_contacts')->where('id', $id)->delete();
                event(new NewClientActivity($client_id, ' has deleted the contact (' . $contact->name . ') for this client.'));
                $request->session()->flash("status", "Client Contact deleted successfully!");
                return redirect('/clients/view/'.$client_id.'#contacts');
            } else {
                throw new Exception('Contact not found...');
            }
        } catch(Exception $e) {
            $request->session()->flash("status", $e->getMessage());
            return redirect()->back();
        }
    }
}