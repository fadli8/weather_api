<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class forecast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'forecast {city=Santander} {country_code=ES} {--d|days=1 } {--u|units=metric}';
    protected $signature = 'forecast {location=Santander,ES} {--d|days=1 } {--u|units=metric}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the weather forecast for max 5 days for the given location.';

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
        
        $days = $this->option('days');
        $unit = $this->option('units');

        // return $this->info($unit);

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

        // return $this->info($mess);

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
