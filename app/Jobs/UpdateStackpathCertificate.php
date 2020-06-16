<?php

namespace brain\Jobs;

use Exception;
use brain\Http\StackpathAPI;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use SSH;

class UpdateStackpathCertificate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $nodeIp;
    protected $stackpathId;
    protected $stackpathEndpoint;
    protected /** @noinspection SummerTimeUnsafeTimeManipulationInspection */
        $renewal_buffer = 24 * 60 * 60 * 14; // Time buffer between cert expiration and when to attempt renewal, in seconds
    protected $certFiles = [];
    protected $sslCertId;

    /**
     * Create a new job instance.
     *
     * @param $nodeIp
     * @param $stackpathId
     */
    public function __construct($nodeIp, $stackpathId)
    {
        $this->nodeIp = $nodeIp;
        $this->stackpathId = $stackpathId;
        $this->stackpathEndpoint = "/sites/$stackpathId/ssl";
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $api = new StackpathAPI(env('STACKPATH_ALIAS'), env('STACKPATH_KEY'), env('STACKPATH_SECRET'));
        $currentSslInfo = $this->getSslInfo($api);
        if (empty($currentSslInfo)) {
            Log::error("Failed to get current SSL info for stackpath ID: $this->stackpathId");
            return;
        }
        $this->sslCertId = $currentSslInfo->data->ssl->id;
        if ($this->needsRenewal($currentSslInfo) === false) {
            Log::info("SSL certs are up to date. No update needed for id $this->stackpathId");
            return;
        }
        $retrieved_certs = $this->getCertsFromServer();
        if (!$retrieved_certs) {
            Log::error("Failed to get certs from ip $this->nodeIp");
            return;
        }
        if ($this->uploadCerts($api)) {
            Log::info("Successfully updated stackpath certs for id $this->stackpathId.");
        } else {
            Log::error("Failed to update stackpath certs for id $this->stackpathId.");
        }
    }

    public function failed(Exception $exception)
    {
        Log::error('Failed to update stackpath SSL info: '. $exception->getMessage());
    }

    /**
     * @param StackpathAPI $api
     * @return bool|mixed
     */
    private function getSslInfo(StackpathAPI $api)
    {
        $response_json = $api->get($this->stackpathEndpoint);
        $response = json_decode($response_json);
        if ($response->code !== 200) {
            Log::error('Failed to get ssl info: ' . $response_json);
            return false;
        }
        return $response;
    }

    /**
     * @param $currentSslInfo
     * @return bool
     */
    private function needsRenewal($currentSslInfo): bool
    {
        $date = strtotime($currentSslInfo->data->ssl->date_expiration);
        return $date - $this->renewal_buffer < now()->timestamp;
    }

    /**
     * @return bool
     */
    private function getCertsFromServer(): bool
    {
        if (empty($this->nodeIp)) {
            return false;
        }

        Config::set('remote.connections.runtime.host', $this->nodeIp);

        $filePath = '/etc/letsencrypt/live/';

        $getDirectoryCommands = [
            'cd ' . $filePath,
            'ls | cut -f 1'
        ];
        SSH::into('runtime')->run($getDirectoryCommands, function ($line) use (&$filePath) {
            $filePath .= trim($line) . '/';
        });

        $this->certFiles['cert'] = SSH::into('runtime')->getString($filePath . 'cert.pem');
        $this->certFiles['privkey'] = SSH::into('runtime')->getString($filePath . 'privkey.pem');
        $this->certFiles['chain'] = SSH::into('runtime')->getString($filePath . 'chain.pem');

        return true;
    }

    /**
     * @param StackpathAPI $api
     * @return bool
     */
    private function uploadCerts(StackpathAPI $api): bool
    {
        $params = [
            'ssl_id' => $this->sslCertId,
            'ssl_crt' => $this->certFiles['cert'],
            'ssl_key' => $this->certFiles['privkey'],
            'ssl_cabundle' => $this->certFiles['chain'],
            'ssl_sni' => 1
        ];
        $response = json_decode($api->put($this->stackpathEndpoint, $params));

        return $response->code === 200;
    }
}
