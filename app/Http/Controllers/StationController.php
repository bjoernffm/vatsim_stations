<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $stations = $request->input('stations');
        if($stations == null or $stations == '') {
            abort(403);
        }
        
        $stations = explode(',', $stations);
        for($i = 0; $i < count($stations); $i++) {
            $stations[$i] = trim($stations[$i]);
        }

        $key = implode($stations);
        $value = Cache::remember($key, 60, function() use ($stations) {
            $client = new \GuzzleHttp\Client();
        
            $response = $client->get('http://data.vatsim.net/vatsim-data.json');

            if ($response->getStatusCode() != 200) {
                abort(500);
            }
            $body = (string) $response->getBody();
            $json = json_decode($body, true);

            $list = [];

            foreach($json['clients'] as $client) {
                if ($client['clienttype'] != 'PILOT' and in_array($client['callsign'], $stations)) {
                    $list[] = $client;
                }
            }
            return $list;
        });

        return $value;
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
