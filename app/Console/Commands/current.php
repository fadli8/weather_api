<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class current extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'current {city=Santander},{country_code=ES} {--u|units=metric}';
    protected $signature = 'current {location=Santander,ES} {--u|units=metric}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the current weather data for the given location.';

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

        $unit = $this->option('units');

        $response = Http::get("https://api.openweathermap.org/data/2.5/weather?q=$city,$country&units=$unit&appid=ae9b6288ec7564dc8d72018287bb5456");

        $res = json_decode($response, true);
        $d =date("F d, Y",$res['dt']);
    
        $mess = "$city,($country) \n";
        $mess .= $d ."\n";
        $mess .= "> Weather: ".$res['weather'][0]['description']."\n";
        $mess .= "> Temperature: ".$res['main']['temp'] . ($unit == "imperial"?" °F":"°C");
     
        
        // json_decode()
        // dd($jsonData);
        
        return $this->info($mess);
    }
}
