<?PHP

namespace Denngarr\Seat\Billing\Helpers;

use Illuminate\Support\Facades\DB;
use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Eveapi\Models\Industry\CharacterMining;
use Seat\Eveapi\Models\Corporation\CorporationInfo;
use Seat\Eveapi\Models\Wallet\CorporationWalletJournal;
use Seat\Services\Models\UserSetting;
use Seat\Services\Repositories\Corporation\Members;
use Seat\Web\Models\User;
use Denngarr\Seat\Billing\Models\CharacterBill;
use Denngarr\Seat\Billing\Models\CorporationBill;


trait BillingHelper {

use Members;

public function getCharacterBilling($character_id, $year, $month) {

    $ledger = CharacterMining::select('character_minings.character_id', 'year', 'month', 'type_id', 'quantity')
        ->join('corporation_member_trackings', 'corporation_member_trackings.character_id', 'character_minings.character_id')
        ->where('character_minings.character_id', $character_id)
        ->where('year', $year)
        ->where('month', $month)
        ->get();

    $ledger->groupBy('character_id')
        ->map(function ($row) {
      	$row->quantity = $row->sum('quantity');
       	$row->volume = $row->sum('volume');
	$row->amount = $row->sum('amount');

	return $row;
        })->flatten();

    return $ledger->sum('amount');
}

public function getCharacterTaxRate($character_id) {
    $registered = 0;
    $character = CharacterInfo::where('character_id', $character_id)->first();
    $corp = CorporationInfo::where('corporation_id', $character->corporation_id)->first();

    if ($corp != null) {
        $tracking = $this->getTrackingMembers($corp->corporation_id);

        foreach ($tracking as $member) {
            if ($member->key_ok) {
                $registered++;
            }
        }

        $total = count($tracking) | 1;

        if (($registered / $total) < (setting('irate', true) / 100)) {
            $taxrate = setting('oretaxrate', true);
        } else {
            $taxrate = setting('ioretaxrate', true);
        }
        return ($taxrate / 100);
    }
    return 0;
}

private function getTrackingMembers($corporation_id) {

    return $this->getCorporationMemberTracking($corporation_id);
}

public function getMainsBilling($corporation_id) {

$summary = [];
$main_ids = UserSetting::where('name', 'main_character_id')
  ->select('value')
  ->get()
  ->toArray();

foreach ($main_ids as $key => $id) {
  $amount = 0;
  $main = User::where('id', $id)
      ->first();
  $characters = $main->associatedCharacterIds();

  foreach ($characters as $character_id) {
      $char = CharacterInfo::where('character_id', $character_id)->first();
      if ($char->corporation_id == $corporation_id) {
	  if (!isset($summary[$main->character_id])) {
	      $summary[$main->character_id]['amount'] = 0;
	  }
	  $amount = $this->getCharacterBilling($character_id, date('Y'), date('n')-1);

	  $summary[$main->character_id]['amount'] += $amount;
	  $summary[$main->character_id]['id'] = $character_id;
	  $summary[$main->character_id]['taxrate'] = $this->getCharacterTaxRate($character_id);
      }
  }
}

return $summary;
}

//                $mining_taxrate = $this->getMiningTaxRate($corp->corporation_id);
//                $pve_taxrate = $this->getPveTaxRate($corp->corporation_id);

public function getCorporateTaxRate($corporation_id) {

    $tracking = $this->getTrackingMembers($corporation_id);
    $total_chars = count($tracking);
    if ($total_chars == 0) {
        $total_chars = 1;
    }
    $reg_chars = 0;

    foreach ($tracking as $member) {
        if ($member->key_ok) {
            $reg_chars++;
        }
    }

    if (($reg_chars / $total_chars) < (setting('irate', true) / 100)) {
        $mining_taxrate = setting('oretaxrate', true);
        $mining_modifier = setting('oremodifier', true);
        $pve_taxrate = setting('pvetaxrate', true);
    } else {
        $mining_taxrate = setting('ioretaxrate', true);
        $mining_modifier = setting('ioremodifier', true);
        $pve_taxrate = setting('ipvetaxrate', true);
    }
    return ['taxrate' => $mining_taxrate, 'modifier' => $mining_modifier, 'pve' => $pve_taxrate];
}

    private function getMiningTotal($corporation_id, $year, $month) {

        $ledgers = $this->getCorporationLedger($corporation_id, $year, $month, true)
            ->groupBy('character_id')
            ->map(function ($row) {

                $row->quantity = $row->sum('quantity');
                $row->volumes = $row->sum('volumes');
                $row->amount = $row->sum('amount');

                return $row;
            });

        return $ledgers->sum('amount');
    }


    private function getBountyTotal($corporation_id, $year, $month) {

        $bounties = $this->getCorporationLedgerBountyPrizeByMonth($corporation_id, $year, $month);

        return $bounties->sum('total');
    }

    private function getCorporationBillingMonths($corporation_id) {

        if (!is_array($corporation_id)) {
            array_push($corporation_ids, $corporation_id);
        } else {
            $corporation_ids = $corporation_id;
        }
       
        return CorporationBill::select(DB::raw('DISTINCT month, year'))
            ->wherein('corporation_id', $corporation_ids)
            ->orderBy('month', 'year', 'desc')
            ->get();

    }
  
    private function getCorporationBillByMonth($corporation_id, $year, $month) {
        return CorporationBill::where("corporation_id", $corporation_id)
            ->where("month", $month)
            ->where("year", $year)
            ->first();
    }

    private function getPastMainsBillingByMonth($corporation_id, $year, $month) {
        return CharacterBill::where("corporation_id", $corporation_id)
            ->where("month", $month)
            ->where("year", $year)
            ->get();
    }

    // select id from corporation_wallet_journals 
    // INNER JOIN character_infos on corporation_wallet_journals.first_party_id=character_infos.character_id 
    // WHERE corporation_wallet_journals.amount='9000000000' 
    // and character_infos.corporation_id='98387096' 
    // and corporation_wallet_journals.ref_type='player_donation';

    private function getPaidBillFromJournal($corporation_id, $amount, $month, $year) {
       $val = CorporationWalletJournal::join('character_infos', 'corporation_wallet_journals.first_party_id', '=', 'character_infos.character_id')
          ->where('corporation_wallet_journals.amount', round($amount, 2))
          ->where('character_infos.corporation_id', $corporation_id)
          ->where('corporation_wallet_journals.ref_type', 'player_donation')
          ->whereMonth('corporation_wallet_journals.date', $month+1)
          ->whereYear('corporation_wallet_journals.date', $year)
          ->select('id')
          ->get();

     return $val;
    }
}
