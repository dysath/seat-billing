<?php

namespace Denngarr\Seat\Billing\Commands;

use Illuminate\Console\Command;
use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Eveapi\Models\Industry\CharacterMining;
use Seat\Eveapi\Models\Corporation\CorporationInfo;
use Seat\Services\Repositories\Character\MiningLedger as CharacterLedger;
use Seat\Services\Repositories\Corporation\Ledger;
use Seat\Services\Repositories\Corporation\MiningLedger;
use Denngarr\Seat\Billing\Models\CharacterBill;
use Denngarr\Seat\Billing\Models\CorporationBill;
use Denngarr\Seat\Billing\Helpers\BillingHelper;

class BillingUpdate extends Command
{
    use BillingHelper, Ledger, MiningLedger;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This records the bills for the previous month if one doesn\'t exist.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lastmonth = date('Y-n', strtotime('-1 month'));
        list($year, $month) = preg_split("/-/", $lastmonth, 2);

        // See if corps already have a bill.  If not, generate one.

        $corps = CorporationInfo::all();
        foreach ($corps as $corp) {
            $bill = CorporationBill::where('corporation_id', $corp->corporation_id)
                ->where('year', $year)
                ->where('month', $month)
                ->get();

            if (count($bill) == 0) {
                if (!$corp->tax_rate) {
                    $corp_taxrate = .10;
                } else {
                    $corp_taxrate = $corp->tax_rate;
                }
                $rates = $this->getCorporateTaxRate($corp->corporation_id);

                $bill = new CorporationBill;
                $bill->corporation_id = $corp->corporation_id;
                $bill->year = $year;
                $bill->month = $month;
                $bill->mining_bill = $this->getMiningTotal($corp->corporation_id, $year, $month);
                $bill->pve_bill = $this->getBountyTotal($corp->corporation_id, $year, $month) / $corp_taxrate;
                $bill->mining_taxrate = $rates['taxrate'];
                $bill->mining_modifier = $rates['modifier'];
                $bill->pve_taxrate = $rates['pve'] | 10;
                $bill->save();
            }

            $summary = $this->getMainsBilling($corp->corporation_id, $year, $month);
            $rates = $this->getCorporateTaxRate($corp->corporation_id);

            foreach ($summary as $character) {
                $bill = CharacterBill::where('character_id', $character['id'])
                    ->where('year', $year)
                    ->where('month', $month)
                    ->get();
                if (count($bill) == 0) {
                    $bill = new CharacterBill;
                    $bill->character_id = $character['id'];
                    $bill->corporation_id = $corp->corporation_id;
                    $bill->year = $year;
                    $bill->month = $month;
                    $bill->mining_bill = $character['amount'];
                    $bill->mining_taxrate = ($character['taxrate'] * 100);
                    $bill->mining_modifier = $rates['modifier'];
                    $bill->save();
                }
            }
        }
    }
}
