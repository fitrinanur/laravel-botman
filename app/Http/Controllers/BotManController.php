<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use App\Conversations\ExampleConversation;
use GuzzleHttp\Client;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;

class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    
    public function handle()
    {
        $botman = app('botman');
        
        $config = [
            "telegram" => [
               "token" => "516359961:AAFnnMIdGWTAwDog0UtuDV-Rg_gFaJX9bbc"
            ]
        ];
        
        // Load the driver(s) you want to use
        DriverManager::loadDriver(\BotMan\Drivers\Telegram\TelegramDriver::class);
        
        // Create an instance
        $botman = BotManFactory::create($config);
        

        $botman->hears('Hello', function (BotMan $bot) {
            $bot->types();
            $bot->reply('Hi there :)');
        });

        $botman->hears('I love you', function (BotMan $bot) {
            $bot->types();
            $bot->reply('Love You Too..');
        });
      
        $botman->hears('Give me {currency} rates', function ($bot, $currency) {
            $bot->types();
            $results = $this->getCurrency($currency);
            $bot->reply($results);
        });
      
        $botman->fallback(function($bot) {
            $bot->types();
            $bot->reply('Sorry, I did not understand these commands. Please retype again...');
        });

        $botman->listen();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker()
    {
        return view('tinker');
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function startConversation(BotMan $bot)
    {
        $bot->startConversation(new ExampleConversation());
    }

    public function getCurrency($currency)
    {
        $client = new Client();
        $uri = 'http://api.fixer.io/latest?base='.$currency;
        $response = $client->get($uri);          
        $results = json_decode($response->getBody()->getContents());
        $date = date('d F Y', strtotime($results->date));
        $data = "Here's the exchange rates based on ".$currency." currency\nDate: ".$date."\n";
        foreach($results->rates as $k => $v) {
            $data .= $k." - ".$v."\n";
        }
        return $data;
    }
}
