<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class forecastAsk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forecast:ask {location=Santander,ES}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Same as forecast command using questions for days and unit of measure';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $location = $this->argument('location');
        $res = explode(",", $location);
        $city = $res[0];
        $country = $res[1];   
        
        $days = $this->ask('How many days to forecast?');
        if($days < 1 || $days > 5){
            return $this->warn('thank you for choosing a number between 1 and 5');
        }
        $unitsQ = $this->ask("what unit of measure?\n[0] metric\n[1] imperial");
       
        if($unitsQ >=0 && $unitsQ <= 1){
            if($unitsQ == 0){
                $unit = 'metric';
            }else if($unitsQ == 1){
                $unit = $unitsQ == 0?'metric':'imperial';
            }
        }else{
            return $this->warn('thank you for choosing a number between 0 and 1');
        }

        $response = Http::get("https://api.openweathermap.org/data/2.5/forecast?q=$city,$country&units=$unit&appid=ae9b6288ec7564dc8d72018287bb5456");
        $res = json_decode($response->body(), true);

        $date = date('d/m/Y', $res['list'][0]['dt']);
        $d =date("F d, Y",$res['list'][0]['dt']);
        $counter = 1;

        $mess = "$city,($country) \n";
        $mess .= $d ."\n";
        $mess .= "> Weather: ".$res['list'][0]['weather'][0]['description']."\n";
        $mess .= "> Temperature: ".$res['list'][0]['main']['temp'] . ($unit == "imperial"?" °F\n":"°C\n");
        $mess .= "\n";

        for ($i = 1; $i < count($res['list']); $i++) {
            if($counter < $days){
                $newDate = date('d/m/Y', $res['list'][$i]['dt']);
                if($date != $newDate){
                    $counter++;
                    $date = $newDate;
    
                    $d =date("F d, Y",$res['list'][$i]['dt']);
        
                    $mess .= "$city,($country) \n";
                    $mess .= $d ."\n";
                    $mess .= "> Weather: ".$res['list'][$i]['weather'][0]['description']."\n";
                    $mess .= "> Temperature: ".$res['list'][$i]['main']['temp'] . ($unit == "imperial"?" °F\n":"°C\n");
                    $mess .= "\n";
                }
            }
        }

        return $this->info($mess);
    }
}
