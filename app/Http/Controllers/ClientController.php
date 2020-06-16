<?php

namespace brain\Http\Controllers;

use brain\Events\NewClientActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;

class ClientController extends Controller {

	public function __construct() {
		$this->middleware('auth');
	}

	public function index(Request $request) {
		$clients = DB::table('clients')->where('status', 1)->orderBy('name')->get();

		foreach($clients as $client) {
		    $client->cats = DB::table('client_categories as cc')
                ->whereIn('cc.id', function($q) use($client) {
                    $q->select('client_category_id')
                        ->from('clients_client_categories')
                        ->where('client_id', $client->id);
                })
                ->orderBy('cc.title')
                ->get();
        }
        $data['clients'] = $clients;

		return view('clients.index', $data);
	}

    public function view(Request $request, $id) {
        $data['client'] = DB::table('clients')
            ->where('id', $id)
            ->first();

        // Category Data
        $data['cat_ids'] = DB::table('client_categories as cc')
            ->join('clients_client_categories as ccc', 'ccc.client_category_id', '=', 'cc.id')
            ->where('ccc.client_id', $id)
            ->pluck('cc.id')
            ->toArray();

        $data['selected_cats'] = DB::table('client_categories')
            ->whereIn('id', $data['cat_ids'])
            ->orderBy('title')
            ->get();

        $data['categories'] = DB::table('client_categories')
            ->orderBy('title')
            ->get();

        // Credential Data
        $data['creds'] = DB::table('client_credentials')
            ->where('client_id', $id)
            ->where('label', '<>', '')
            ->orderBy('sort')
            ->get();

        $data['imgs'] = DB::table('client_credentials')
            ->where('client_id', $id)
            ->where('image_url', '<>', '')
            ->get();


        // Contact Data
        $types = DB::table('client_contacts')->select('contact_type')->pluck('contact_type');
        $typeArr = [];
        foreach($types as $type) {
            $sTypes = explode(',', $type);
            foreach($sTypes as $sType) {
                if(!in_array($sType, $typeArr)) {
                    $typeArr[] = ['text' => $sType, 'value' => $sType];
                }
            }
        }
        $data['types'] = $typeArr;
        $data['contacts'] = DB::table('client_contacts')
            ->where('client_id', $id)
            ->get();

        // Note Data
        $data['notes'] = DB::table('client_notes')->where('client_id', $id)->orderBy('updated_at', 'desc')->get();

        return view('clients.view', $data);
    }

	public function save(Request $request) {

	    try {
            $this->validate($request, [
                'name' => 'required|unique:clients,name',
                'url' => 'required',
                'description' => 'required'
            ]);

            $id = DB::table('clients')->insertGetId([
                'name' => $request->get('name'),
                'url' => rtrim(preg_replace("(^https?://)", "", $request->get('url') ), '/'),
                'description' => $request->get('description'),
                'billable_rate' => $request->get('billable_rate'),
                'hosting' => $request->get('hosting'),
                'is_mm' => (($request->get('is_mm')) ? 1 : 0),
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
            ]);

            event(new NewClientActivity($id, ' has created this Client Record.'));

            $request->session()->flash("status", "Client created successfully!");
            return redirect('/clients');

        } catch(\Exception $e) {
            $request->session()->flash("status", $e->getMessage());
            return redirect()->back()->withInput();
        }
	}

	public function update(Request $request, $id) {

	    try {
            $this->validate($request, [
                'name' => 'required|unique:clients,name,'.$id,
                'url' => 'required',
                'description' => 'required'
            ]);

            DB::table('clients')->where('id', $id)->update([
                'name' => $request->get('name'),
                'url' => rtrim(preg_replace("(^https?://)", "", $request->get('url') ), '/'),
                'description' => $request->get('description'),
                'billable_rate' => $request->get('billable_rate'),
                'hosting' => $request->get('hosting'),
                'is_mm' => (($request->get('is_mm')) ? 1 : 0),
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
            ]);

            DB::table('clients_client_categories')->where('client_id', $id)->delete();
            $categories = $request->get('categories');
            if(!empty($categories)) {
                foreach($categories as $cat_id) {
                    DB::table('clients_client_categories')->insert([
                        'client_id' => $id,
                        'client_category_id' => $cat_id
                    ]);
                }
            }

            event(new NewClientActivity($id, ' has updated the details for this client.'));

            $request->session()->flash("status", "Client updated successfully!");
            return redirect('/clients/view/'.$id);

        } catch(\Exception $e) {
            $request->session()->flash("status", $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
}