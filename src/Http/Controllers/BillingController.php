<?PHP

namespace Denngarr\Seat\Billing\Http\Controllers;

use Illuminate\Http\Request;
use Seat\Web\Http\Controllers\Controller;
use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Eveapi\Models\Industry\CharacterMining;
use Seat\Eveapi\Models\Corporation\CorporationInfo;
use Seat\Services\Repositories\Character\MiningLedger as CharacterLedger;
use Seat\Services\Repositories\Corporation\Ledger;
use Seat\Services\Repositories\Corporation\MiningLedger;
use Seat\Services\Repositories\Corporation\Members;
use Seat\Web\Models\User;
use Denngarr\Seat\Billing\Validation\ValidateSettings;
use Denngarr\Seat\Billing\Helpers\BillingHelper;

class BillingController extends Controller {

    use MiningLedger, Ledger, CharacterLedger, BillingHelper;

    public function getLiveBillingView() {
        $summary = [];
   
        $corporations = $this->getCorporations();
        foreach ($corporations as $corporation) {
            $summary[$corporation->name]['id'] = $corporation->corporation_id;
            $summary[$corporation->name]['mining'] = $this->getMiningTotal($corporation->corporation_id, date('Y'), date('n'));
            $summary[$corporation->name]['tracking'] = 0;

            $tracking = $this->getTrackingMembers($corporation->corporation_id); 
            $summary[$corporation->name]['characters'] = count($tracking);
             
            foreach ($tracking as $member) {
                if ($member->key_ok) {
                    $summary[$corporation->name]['tracking']++;
                }
            } 
            if (!$corporation->tax_rate) {
                $summary[$corporation->name]['corptaxrate'] = .10;
            } else {
                $summary[$corporation->name]['corptaxrate'] = $corporation->tax_rate;
            }
            $summary[$corporation->name]['bounty'] = $this->getBountyTotal($corporation->corporation_id, date('Y'), date('n')) / $summary[$corporation->name]['corptaxrate'];
             
            if ($summary[$corporation->name]['characters'] == 0) {
                $summary[$corporation->name]['characters'] = 1;
            }
 
            if (($summary[$corporation->name]['tracking'] / $summary[$corporation->name]['characters']) < (setting('irate', true) / 100)) {
                $summary[$corporation->name]['oretaxrate'] = setting('oretaxrate', true);
                $summary[$corporation->name]['oremodifier'] = setting('oremodifier', true);
                $summary[$corporation->name]['bountytaxrate'] = setting('bountytaxrate', true);
            } else {
                $summary[$corporation->name]['oretaxrate'] = setting('ioretaxrate', true);
                $summary[$corporation->name]['oremodifier'] = setting('ioremodifier', true);
                $summary[$corporation->name]['bountytaxrate'] = setting('ibountytaxrate', true);
            }
        }

        $dates = $this->getCorporationBillingMonths($corporations->pluck('corporation_id')->toArray());

        return view('billing::summary', compact('summary', 'dates'));
    }

    private function getCorporations() {

        if (auth()->user()->hasSuperUser()) {
            $corporations = CorporationInfo::all();
        } else {
            $corpids = CharacterInfo::whereIn('character_id', auth()->user()->associatedCharacterIds())
                 ->select('corporation_id')
                 ->get()
                 ->toArray();
            $corporations = CorporationInfo::whereIn('corporation_id', $corpids)->get();
        }

        return $corporations;
    }

    public function getBillingSettings() {

        return view('billing::settings');
    }

    public function saveBillingSettings(ValidateSettings $request) {

        setting(["oremodifier", $request->oremodifier], true);
        setting(["oretaxrate", $request->oretaxrate], true);
        setting(["bountytaxrate", $request->bountytaxrate], true);
        setting(["ioremodifier", $request->ioremodifier], true);
        setting(["ioretaxrate", $request->ioretaxrate], true);
        setting(["ibountytaxrate", $request->ibountytaxrate], true);
        setting(["irate", $request->irate], true);

        return view('billing::settings');
    }

    public function getUserBilling($corporation_id) {

      $summary = $this->getMainsBilling($corporation_id);

      return $summary;
    }

    public function getPastUserBilling($corporation_id, $year, $month) {

      $summary = $this->getPastMainsBillingByMonth($corporation_id, $year, $month);

      return $summary;
    }

    public function previousBillingCycle($year, $month) {
        $summary = [];

        $corporations = $this->getCorporations();

        foreach($corporations as $corporation) {
           $summary[$corporation->corporation_id]['id'] = $corporation->corporation_id;
           $summary[$corporation->corporation_id]['name'] = $corporation->name;

           $bill = $this->getCorporationBillByMonth($corporation->corporation_id, $year, $month);

           $summary[$corporation->corporation_id]['pve_bill'] = $bill->pve_bill;
           $summary[$corporation->corporation_id]['mining_bill'] = $bill->mining_bill;
           $summary[$corporation->corporation_id]['pve_taxrate'] = $bill->pve_taxrate;
           $summary[$corporation->corporation_id]['mining_taxrate'] = $bill->mining_taxrate;
           $summary[$corporation->corporation_id]['mining_modifier'] = $bill->mining_modifier;
           if (count($this->getPaidBillFromJournal($corporation->corporation_id, ($bill->pve_bill * ($bill->pve_taxrate / 100)), $month, $year)) === 0 ) {
               $summary[$corporation->corporation_id]['pve_paid'] = false;
           } else {
               $summary[$corporation->corporation_id]['pve_paid'] = true;
           }

           if (count($this->getPaidBillFromJournal($corporation->corporation_id, ($bill->mining_bill * ($bill->mining_modifier / 100) * ($bill->mining_taxrate / 100)), $month, $year)) === 0) {
               $summary[$corporation->corporation_id]['mining_paid'] = false;
           } else {
               $summary[$corporation->corporation_id]['mining_paid'] = true;
           }
        }        
        
        
        $dates = $this->getCorporationBillingMonths($corporations->pluck('corporation_id')->toArray());
        return view('billing::pastbill', compact('summary', 'dates', 'year', 'month'));
    }
}
