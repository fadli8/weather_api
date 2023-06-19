<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class weatherController extends Controller
{
    function getData() {
        $response = Http::get("https://api.openweathermap.org/data/2.5/weather?q=Mohammedia,MA&units=imperial&appid=ae9b6288ec7564dc8d72018287bb5456");
       $res = json_decode($response->body(), true);
        // retuen `Havana,SP
        // date()`
        $d =date("F d, Y",$res['dt']);
    
        $mess = "Havana,(SP) <br />";
        $mess .= $d ."<br />";
        $mess .= "> Weather: ".$res['weather'][0]['description']."<br />";
        $mess .= "> Temperature: ".$res['main']['temp'] ." °F";
        return $mess;
        // return date(intToTime($res['dt']));
    }

    function dataOfFiveDays() {
        $city = "Mohammedia";
        $country = "MA";
        $days = 5;
        $unit = 'imperial';

        $response = Http::get("https://api.openweathermap.org/data/2.5/forecast?q=$city,$country&units=$unit&appid=ae9b6288ec7564dc8d72018287bb5456");
        $res = json_decode($response->body(), true);
        $date = date('d/m/Y', $res['list'][0]['dt']);
        $d =date("F d, Y",$res['list'][0]['dt']);
        $counter = 1;
        
        $mess = "$city,($country) <br/>";
        $mess .= $d ."<br/>";
        $mess .= "> Weather: ".$res['list'][0]['weather'][0]['description']."<br/>";
        $mess .= "> Temperature: ".$res['list'][0]['main']['temp'] . ($unit == "imperial"?" °F<br/>":"°C<br/>");
        $mess .= "----------------------<br/>";
        
        for ($i = 1; $i < count($res['list']); $i++) {

            if($counter < 5){
                $newDate = date('d/m/Y', $res['list'][$i]['dt']);
                if($date != $newDate){
                    $counter++;
                    $date = $newDate;
    
                    $d =date("F d, Y",$res['list'][$i]['dt']);
        
                    $mess .= "$city,($country) <br/>";
                    $mess .= $d ."<br/>";
                    $mess .= "> Weather: ".$res['list'][$i]['weather'][0]['description']."<br/>";
                    $mess .= "> Temperature: ".$res['list'][$i]['main']['temp'] . ($unit == "imperial"?" °F<br/>":"°C<br/>");
                    $mess .= "----------------------<br/>";
                }
            }
        }

        return $mess;
    }
}
