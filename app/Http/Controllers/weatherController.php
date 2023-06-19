<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class weatherController extends Controller
{
    function currentWeather(Request $request) {

        try {
            $location = $request->location?$request->location:'Santander,ES';
            $res = explode(",", $location);
            $city = $res[0];
            $country_code = $res[1];
            $unit = $request->unit?$request->unit:'metric';

            $response = Http::get("https://api.openweathermap.org/data/2.5/weather?q=$city,$country_code&units=$unit&appid=ae9b6288ec7564dc8d72018287bb5456");
            $res = json_decode($response->body(), true);

            $date =date("F d, Y",$res['dt']);
        
            $mess = "$city,($country_code) <br />";
            $mess .= $date ."<br />";
            $mess .= "> Weather: ".$res['weather'][0]['description']."<br />";
            $mess .= "> Temperature: ".$res['main']['temp'] ." °F";
        
            return $mess;
        } catch (\Exception $err) {
            throw $err;
        }
    }

    function forecast(Request $request) {
       try {
            $location = $request->location?$request->location:'Santander,ES';
            $res = explode(",", $location);
            $city = $res[0];
            $country = $res[1];
            $unit = $request->unit?$request->unit:'metric';
    
            $days = $request->cnt?$request->cnt:1;
          

            $response = Http::get("https://api.openweathermap.org/data/2.5/forecast?q=$city,$country&units=$unit&appid=ae9b6288ec7564dc8d72018287bb5456");
            $res = json_decode($response->body(), true);
            
            $dateSlashed = date('d/m/Y', $res['list'][0]['dt']);
            $date =date("F d, Y",$res['list'][0]['dt']);
            $counter = 1;
            
            $mess = "$city,($country) <br/>";
            $mess .= $date ."<br/>";
            $mess .= "> Weather: ".$res['list'][0]['weather'][0]['description']."<br/>";
            $mess .= "> Temperature: ".$res['list'][0]['main']['temp'] . ($unit == "imperial"?" °F<br/>":"°C<br/>");
            $mess .= "----------------------<br/>";
            
            for ($i = 1; $i < count($res['list']); $i++) {
                if($counter < $days){
                    $newDate = date('d/m/Y', $res['list'][$i]['dt']);
                    if($dateSlashed != $newDate){
                        $counter++;
                        $dateSlashed = $newDate;
        
                        $date =date("F d, Y",$res['list'][$i]['dt']);
            
                        $mess .= "$city,($country) <br/>";
                        $mess .= $date ."<br/>";
                        $mess .= "> Weather: ".$res['list'][$i]['weather'][0]['description']."<br/>";
                        $mess .= "> Temperature: ".$res['list'][$i]['main']['temp'] . ($unit == "imperial"?" °F<br/>":"°C<br/>");
                        $mess .= "----------------------<br/>";
                    }
                }
            }

            return $mess;
       } catch (\Exception $err) {
            throw $err;
       }
    }
}
