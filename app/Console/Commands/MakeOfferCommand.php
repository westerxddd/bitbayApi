<?php

namespace App\Console\Commands;

use App\Models\Offer;
use App\Services\BitbayService;
use App\Services\OfferService;
use Illuminate\Console\Command;

class MakeOfferCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitbay:offer {market} {type} {amount} {rate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle(BitbayService $bitbayService, OfferService $offerService)
    {
        $amount = $this->argument('amount');
        $rate = $this->argument('rate');

        $offer = Offer::create([
            'market' => $this->argument('market'),
            'type' => $this->argument('type'),
            'amount' => $amount,
            'rate' => $rate,
        ]);

        $bitbayApi = $bitbayService->login();
        $try = 0;
        while (!$offer->completed && !$offer->errors){
            $ticker = json_decode($bitbayApi->getTicker($offer->market), true);
            if ($ticker['ticker']){
                switch ($offer->type){
                    case 'sell':
                    case 'SELL':
                        if ((float) $ticker['ticker']['rate'] >= $offer->rate){
                            $offer = $offerService->makeOffer($offer, $bitbayApi);
                            $this->info('Oferta sprzedaży została złożona!');
                            die();
                        }
                        break;
                    case 'buy':
                    case 'BUY':
                        if ((float) $ticker['ticker']['rate'] <= $offer->rate){
                            $offer = $offerService->makeOffer($offer, $bitbayApi);
                            $this->info('Oferta kupna została złożona!');
                            die();
                        }
                        break;
                }
                print_r('Próba: '.++$try.PHP_EOL);
                sleep(1);
            } else {
                $this->info('Nie udało się pobrać Ticker\'a!');
                die();
            }
        }
    }
}
