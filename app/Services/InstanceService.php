<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\Machine;
use App\Services\NotificationService;
// Carbon
use Carbon\Carbon;

class InstanceService
{
    private $client;
    private $apiKey;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://console.vast.ai/api/']);
        $this->apiKey = env('VAST_API_KEY');
    }

    public function createInstance()
{
    $searchResult = $this->searchMachines();
    if (empty($searchResult['offers'])) {
        return ['error' => 'No suitable machines found'];
    }    
    $machineNow = $searchResult['offers'][0];
    $offer_id = $machineNow['id'];
    $machineName = str_replace(' ', '-', $machineNow['gpu_name']);
    $machineName = $machineName."-".substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
    $endpoint = 'v0/asks/'.$offer_id.'/';
    
    $params = [
        'client_id' => 'me',
        'image' => 'nvidia/cuda:12.0.1-devel-ubuntu22.04',
        'env' => ['TZ' => 'UTC'],
        'price' => $machineNow['dph_total_adj'],
        'onstart'=> "apt install -y curl nano --no-install-recommends\ncurl -o init.sh https://raw.githubusercontent.com/Kesehet/vastapibkup/main/onstart.sh\nsed -i 's/\\r//g' init.sh\nchmod +x init.sh\n./init.sh\n",
        'disk' => 30,
        'label' => $machineName,
        'runtype' => 'ssh',
        'force' => false
    ];
    $response = $this->sendRequest('PUT', $endpoint, ['json' => $params]);
    $machine = Machine::create([
        'name' => $machineName, 
        'price' => $machineNow['dph_total_adj'], 
        'status' => true, 
        'last_active' => Carbon::now(), 
        'machine_id' => $response["new_contract"]
    ]);
    NotificationService::send("Created:- ".json_encode($response));
    NotificationService::send("I have created a machine for you with the name ".$machine->name." and id ".$machine->machine_id." at the rate of $ ".$machine->price."/hr. Please find the instance status here https://cloud.vast.ai/instances/. Thank you.");
    return $machine;
}

    public function runCommandOnInstance($instanceId, $command){
        $endpoint = "v0/instances/command/{$instanceId}/";
        $params = [
            'command' => $command
        ];

        return $this->sendRequest('PUT', $endpoint, ['json' => $params]);
    }



    public function destroyInstance($instanceId)
    {
        $endpoint = "v0/instances/".$instanceId."/";
        $machine = Machine::where('machine_id', $instanceId)->update(['status' => false, 'last_active' => Carbon::now()]);
        
        NotificationService::send("Destroyed the machine with id ".$instanceId.".");
        return $this->sendRequest('DELETE', $endpoint);
    }
    

    private function searchMachines()
    {
        $criteria = [
            'disk_space' => ['gte' => 16],
            'duration' => ['gte' => 262144],
            'verified' => ['eq' => true],
            'rentable' => ['eq' => true],
            'gpu_ram' => ['gte' => 14263.10042904367],
            'gpu_totalram' => ['gte' => 14766.087579415856],
            'sort_option' => [['dph_total', 'asc'], ['total_flops', 'asc']],
            'order' => [['dph_total', 'asc'], ['total_flops', 'asc']],
            'num_gpus' => ['gte' => 0, 'lte' => 18],
            'allocated_storage' => 16,
            'cuda_max_good' => ['gte' => '12'],
            'limit' => 64,
            'extra_ids' => [],
            'type' => 'ask',
            'direct_port_count' => ['gte' => 2],
            'gpu_name' => ['in'=>['RTX A4000', 'RTX 4060 Ti']],
            "inet_up"=>["gte"=>200.853529061568],
            "inet_down"=>["gte"=>200.01172051333114]
        ];
    
        return $this->sendRequest('GET', 'v0/bundles', ['query' => ['q' => json_encode($criteria)]]);
    }
    

    private function sendRequest($method, $endpoint, $options = [])
    {
        $options = array_merge($options, ['headers' => $this->getDefaultHeaders()]);

        try {
            $response = $this->client->request($method, $endpoint, $options);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function getDefaultHeaders()
    {
        return [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->apiKey
        ];
    }
}
