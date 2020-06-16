<?php

namespace brain\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use brain\Events\NewClientActivity;

class ClientCredentialController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        $client_id = $request->get('client_id');
        try {
            $this->validate($request, [
                'cred_type' => 'required',
                'label' => 'required_if:cred_type,text',
                'value' => 'required_if:cred_type,text',
                'screenshots' => 'required_if:cred_type,image'
            ]);

            $type = $request->get('cred_type');
            switch($type) {
                case 'text':
                    DB::table('client_credentials')->insert([
                        'label' => $request->get('label'),
                        'value' => $request->get('value'),
                        'client_id' => $client_id,
                        'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                        'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
                    ]);
                    event(new NewClientActivity($client_id, ' has created a new text credential (<a href="/clients/view/'.$client_id.'#credentials">'.$request->get('label').'</a>) for this client.'));
                    $request->session()->flash("status", "Credential created successfully!");
                    break;
                case 'image':
                    $files = $request->file('screenshots');
                    $imgNames = [];
                    foreach($files as $file) {
                        $ext = $file->getClientOriginalExtension();
                        $imgName = $client_id.'_'.rand(1000, 9999).'.'.$ext;
                        $path = $file->storeAs('cred_imgs', $imgName);

                        $imgNames[] = $imgName;

                        DB::table('client_credentials')->insert([
                            'image_url' => env('SITE_URL').'/cred_imgs/'.$imgName,
                            'image_path' => $path,
                            'client_id' => $client_id,
                            'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                            'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
                        ]);
                    }
                    event(new NewClientActivity($client_id, ' has created new a screenshot credential(s) (<a href="/clients/view/'.$client_id.'#credentials">'.implode(', ', $imgNames).'</a>) for this client.'));
                    $request->session()->flash("status", "Screenshots saved successfully!");
                    break;
            }

        } catch(\Exception $e) {
            $request->session()->flash("status", $e->getMessage());
        }
        return redirect('/clients/view/'.$client_id.'#credentials');
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
            $client_id = $request->get('client_id');

            DB::table('client_credentials')->where('id', $id)->update([
                'label' => $request->get('label'),
                'value' => $request->get('value')
            ]);

            event(new NewClientActivity($client_id, ' has updated the text credential (<a href="/clients/view/'.$client_id.'#credentials">'.$request->get('label').'</a>) for this client.'));

        } catch(Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
        return response()->json(['success' => true, 'message' => 'Credential changes have been saved!']);
    }

    /**
     * Update sort order of label/value credentials
     *
     * @param \Illuminate\Http\Request  $request
     * @param Request $request
     *
     */
    public function update_sort(Request $request)
    {
        try {
            $ids = $request->get('ids');

            foreach($ids as $idx =>$id) {
                DB::table('client_credentials')->where('id', $id)->update([
                    'sort' => $idx
                ]);
            }
        } catch(Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
        return response()->json(['success' => true, 'message' => 'New Credential Order has been saved!']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $id)
    {
        $type = $request->get('cred_type');
        $client_id = $request->get('client_id');
        switch($type) {
            case 'text':
                $label = DB::table('client_credentials')->where('id', $id)->first()->label;
                DB::table('client_credentials')->where('id', $id)->delete();
                event(new NewClientActivity($client_id, ' has deleted the text credential ('.$label.') for this client.'));
                $request->session()->flash("status", "Credential deleted successfully!");
                return response()->json(['success' => true, 'message' => 'Credential deleted successfully!']);
                break;
            case 'image':
                $img = DB::table('client_credentials')->where('id', $id)->first();
                if(Storage::delete($img->image_path)) {
                    DB::table('client_credentials')->where('id', $id)->delete();
                    event(new NewClientActivity($client_id, ' has deleted the screenshot credential ('.end(explode('/', $img->image_path)).') for this client.'));
                    $request->session()->flash("status", "Screenshots deleted successfully!");
                }
                return redirect('/clients/view/'.$client_id.'#credentials');
                break;
        }
    }
}