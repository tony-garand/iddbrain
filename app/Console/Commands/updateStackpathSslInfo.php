<?php

namespace brain\Console\Commands;

use brain\Jobs\UpdateStackpathCertificate;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\DB;

class updateStackpathSslInfo extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'brain:updateStackpathSSL';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the SSL certificate information for sites connected to stackpath';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //get db's
        $servers = DB::table('servers')
            ->where('node_ip', '!=', '')
            ->where('stackpath_id', '!=', '')
            ->get();
        if (empty($servers)) {
            $this->info('No servers found');
        } else {
            foreach($servers as $server) {
                $this->dispatch(new UpdateStackpathCertificate($server->node_ip, $server->stackpath_id));
                $this->info("Dispatched update job for ip $server->node_ip");
            }
        }
    }
}
