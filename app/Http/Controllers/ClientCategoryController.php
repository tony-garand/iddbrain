<?php

namespace brain\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Mockery\Exception;

class ClientCategoryController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function index() {
        $data['categories'] = DB::table('client_categories')->orderBy('title')->get();
        return view('tools.client_categories.index', $data);
    }

    public function view($id) {
        $data['category'] = DB::table('client_categories')->where('id', $id)->first();
        return view('tools.client_categories.view', $data);
    }

    public function save(Request $request) {
        try {
            $this->validate($request, [
                'title' => 'required|unique:client_categories,title'
            ]);

            DB::table('client_categories')->insert([
                'title' => $request->get('title'),
                'color' => $request->get('color', 'd8242e'),
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
            ]);

            $request->session()->flash("status", "Client Category created successfully!");
            return redirect('/tools/client-categories');
        } catch(Exception $e) {
            $request->session()->flash("status", $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function update(Request $request, $id) {
        try {
            $this->validate($request, [
                'title' => 'required|unique:client_categories,title,'.$id
            ]);

            DB::table('client_categories')->where('id', $id)->update([
                'title' => $request->get('title'),
                'color' => $request->get('color', 'd8242e'),
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
            ]);

            $request->session()->flash("status", "Client Category updated successfully!");
            return redirect('/tools/client-categories');
        } catch(Exception $e) {
            $request->session()->flash("status", $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function delete(Request $request, $id) {
        try {
            DB::table('clients_client_categories')->where('client_category_id', $id)->delete();
            DB::table('client_categories')->where('id', $id)->delete();
        } catch(Exception $e) {
            $request->session()->flash("status", $e->getMessage());
            return redirect()->back();
        }
    }
}
