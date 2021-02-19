<?php

namespace App\Console\Commands;

use App\Mail\NotificationMail;
use App\Services\BitbayService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckCurrentRateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitbay:check-rate {market} {rate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if current market rate is equal or higher than rate input.';

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
    public function handle(BitbayService $bitbayService)
    {
        $market = $this->argument('market');
        $rate = $this->argument('rate');
        $amount = 612.84697671;
        $sellOffer = null;

        $bitbayApi = $bitbayService->login();
        while ($bitbayApi){
            $now = now();
            if ((now()->setSeconds(0)->format('d.m.Y H:i:s') === $now->format('d.m.Y H:i:s'))){
                $ticker = json_decode($bitbayApi->getTicker($market), true);

                if ($ticker['ticker'] && (float) $ticker['ticker']['rate'] >= $rate){
                    $text = $now->format('d-m-Y H:i:s') . 'Aktualny kurs wynosi: '.$ticker['ticker']['rate'];
                    $this->info($text);
                    if (!$sellOffer){
                        $sellOffer = $bitbayApi->createSellOffer($market, $amount, $rate);
                        die();
                    }

                } else {
                    $this->info($now->format('d-m-Y H:i:s') . ' - Błąd!');
                    $this->info('Aktualny kurs ('
                        .number_format((float) $ticker['ticker']['rate'],2, '.', ' ').
                        ') jest niższy od zadanego ('
                        .number_format((float) $rate,2, '.', ' ').
                        ')!');
                }

                sleep(1);
            }
        }
    }
}
