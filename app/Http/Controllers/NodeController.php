<?php

namespace brain\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use SSH;
use Illuminate\Support\Facades\Log;
use GrahamCampbell\DigitalOcean\Facades\DigitalOcean;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use brain\Events\NewClientActivity;

class NodeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        if ((Cache::has('do_droplets')) && (!$request->get('fresh'))) {
            $droplets = Cache::get('do_droplets');
        } else {
            $droplet = DigitalOcean::droplet();
            $droplets = $droplet->getAll();
            Cache::put('do_droplets', $droplets, 1440);
        }

        if ($request->get('fresh')) {
            // flag servers as status = 0 for now..
            DB::table('nodes')->update(['status' => 0]);
        }

        foreach ($droplets as &$drop) {
            foreach ($drop->networks as $net) {
                if ($net->type == "public") {
                    $drop->ip = $net->ipAddress;
                }
            }
            $hide = 0;
            $drop->ram = ($drop->memory / 1024) . " GB";
            $drop->storage = $drop->disk . " GB";
            $drop->location = $drop->region->name;
            foreach ($drop->tags as $tag) {
                if ($tag == "internal") {
                    $hide = 1;
                }
            }
            $drop->hide = $hide;

            if ($drop->hide == 1) {
                $node_status = 0;
            } else {
                $node_status = 1;
            }

            // check if we have this node, if not add it.. if we have it, we may need to update things..
            $match_node = DB::table('nodes')->where('node_id', $drop->id)->first();
            if ($match_node) {

                if ($request->get('fresh')) {
                    // only do the node update if in refresh mode..
                    DB::table('nodes')
                        ->where('id', $match_node->id)
                        ->update([
                                'status' => $node_status,
                                'node_id' => $drop->id,
                                'name' => $drop->name,
                                'ip' => @$drop->ip,
                                'location' => $drop->location,
                                'vcpus' => $drop->vcpus,
                                'ram' => $drop->ram,
                                'storage' => $drop->storage,
                                'memory' => $drop->memory,
                                'disk' => $drop->disk,
                                'node_status' => $drop->status,
                                'node_created' => $drop->createdAt,
                                'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
                            ]
                        );
                }

                // update
            } else {
                $node_id = DB::table('nodes')->insertGetId(
                    [
                        'status' => $node_status,
                        'node_id' => $drop->id,
                        'name' => $drop->name,
                        'ip' => @$drop->ip,
                        'location' => $drop->location,
                        'vcpus' => $drop->vcpus,
                        'ram' => $drop->ram,
                        'storage' => $drop->storage,
                        'memory' => $drop->memory,
                        'disk' => $drop->disk,
                        'node_status' => $drop->status,
                        'node_created' => $drop->createdAt,
                        'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                        'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
                    ]
                );
            }
        }

        $nodes = DB::table('nodes')->select('nodes.*')->where('status', 1)->orderBy('name')->get();
        $clients = DB::table('clients')->select('id', 'name')->get();

        return view('nodes.index', ['droplets' => $droplets, 'nodes' => $nodes, 'clients' => $clients]);
    }

    public function reboot(Request $request, $server_id)
    {

        if ($server_id) {
            $droplet = DigitalOcean::droplet();
            $rebooted = $droplet->reboot($server_id);

            $request->session()->flash("status", "Node rebooted successfully!");
            return redirect('/nodes?fresh=1');
        }

    }

    public function add_key(Request $request)
    {

        $key_content = trim($request->get('key_content'));

        if (($request->get('node_ip')) && ($key_content)) {

            Config::set('remote.connections.runtime.host', $request->get('node_ip'));

            // connect to ip and append incoming key..
            SSH::into('runtime')->run([
                "echo '" . $key_content . "' >> /root/.ssh/authorized_keys"
            ], function ($line) {
                Log::info('Server\SSH (add node key): ' . $line . PHP_EOL);
            });

            $request->session()->flash("status", "Key was added successfully!");
            return redirect('/nodes?fresh=1');

        } else {
            $request->session()->flash("status", "Key was blank!");
            return redirect('/nodes?fresh=1');
        }

    }

    public function add_node(Request $request)
    {

        if ($request->get('node_name')) {

            $ssh_keys = array();
            $key = DigitalOcean::key();
            $keys = $key->getAll();
            
            foreach ($keys as $key) {
                $ssh_keys[] = $key->id;
            }

            $droplet = DigitalOcean::droplet();
            $created = $droplet->create(trim($request->get('node_name')), 'nyc1', 's-1vcpu-2gb', 'ubuntu-16-04-x64',
                true, false, false, $ssh_keys);

            if ($created) {

                $local_node_id = DB::table('nodes')->insertGetId(
                    [
                        'status' => 0,

                        'node_id' => $created->id,
                        'name' => $created->name,
                        'ip' => '',
                        'location' => $created->region->name,
                        'vcpus' => $created->vcpus,
                        'ram' => ($created->memory / 1024) . " GB",
                        'storage' => $created->disk . " GB",

                        'memory' => $created->memory,
                        'disk' => $created->disk,
                        'node_status' => $created->status,
                        'node_created' => $created->createdAt,

                        'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                        'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
                    ]
                );

                // trigger CreateNode job

                $request->session()->flash("status", "Node was added successfully!");
                return redirect('/nodes?fresh=1');


            } else {
                $request->session()->flash("status", "Node could not be created!");
                return redirect('/nodes?fresh=1');
            }


        } else {
            $request->session()->flash("status", "Node name was blank!");
            return redirect('/nodes?fresh=1');
        }

    }

    public function update_node_client(Request $request, $id)
    {
        $client_id = $request->get('client_id');
        
        try {
            $node = DB::table('nodes')->where('node_id', $id)->first();
            $old_client_id = $node->client_id;
            if($node) {
                if (DB::table('nodes')->where('node_id', $id)->update(['client_id' => $client_id])) {
                    if(!empty($client_id)) {
                        event(new NewClientActivity($client_id, ' has added the node ('.$node->name.') to this client.'));
                    }
                    if(empty($client_id) || $client_id != $old_client_id) {
                        event(new NewClientActivity($old_client_id, ' has removed the node ('.$node->name.') from this client.'));
                    }
                    return response()->json(['success' => true, 'message' => 'Node successfully attached to client!']);
                } else {
                    throw new Exception('Unable to update node record');
                }
            } else {
                throw new Exception('Node does not exist');
            }
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}