<?php

namespace Denngarr\Seat\Billing\Models;

use Illuminate\Database\Eloquent\Model;
use Seat\Eveapi\Models\Corporation\CorporationInfo;
use Seat\Eveapi\Models\Wallet\CorporationWalletJournal;

class CorporationBill extends Model
{
    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var string
     */
    protected $table = 'seat_billing_corp_bill';

    /**
     * @var array
     */
    protected $fillable = [
        'id', 'corporation_id', 'month', 'year', 'pve_bill', 'mining_bill',
        'pve_taxrate', 'mining_taxrate', 'mining_modifier'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function corporation()
    {
        return $this->belongsTo(CorporationInfo::class, 'corporation_id', 'corporation_id')
            ->withDefault(function () {
                return new CorporationInfo([
                    'name' => 'Unknown',
                ]);
            });
    }

    /**
     * @return bool
     */
    public function isPaid(): bool
    {
        $pve_amount = $this->pve_bill * ($this->pve_taxrate / 100);
        $mined_amount = $this->mining_bill * ($this->mining_modifier / 100) * ($this->mining_taxrate / 100);
        $searched_amount = $pve_amount + $mined_amount;
        $start_date = carbon(sprintf('%d-%d-01', $this->year, $this->month))->addMonth()->startOfMonth();
        $end_date = carbon(sprintf('%d-%d-01', $this->year, $this->month))->addMonth()->endOfMonth();

        $journal_id = $this->getPaidBillFromJournal(round($searched_amount, 0), $start_date->toDateString(), $end_date->toDateString())
                           ->get();

        return ! $journal_id->isEmpty();
    }

    /**
     * @return bool
     */
    public function isPvePaid(): bool
    {
        $searched_amount = $this->pve_bill * ($this->pve_taxrate / 100);
        $start_date = carbon(sprintf('%d-%d-01', $this->year, $this->month))->addMonth()->startOfMonth();
        $end_date = carbon(sprintf('%d-%d-01', $this->year, $this->month))->addMonth()->endOfMonth();

        $journal_id = $this->getPaidBillFromJournal(round($searched_amount, 0), $start_date->toDateString(), $end_date->toDateString())
                           ->get();

        return ! $journal_id->isEmpty();
    }

    /**
     * @return bool
     */
    public function isMiningPaid(): bool
    {
        $searched_amount = $this->mining_bill * ($this->mining_modifier / 100) * ($this->mining_taxrate / 100);
        $start_date = carbon(sprintf('%d-%d-01', $this->year, $this->month))->addMonth()->startOfMonth();
        $end_date = carbon(sprintf('%d-%d-01', $this->year, $this->month))->addMonth()->endOfMonth();

        $journal_id = $this->getPaidBillFromJournal(round($searched_amount, 0), $start_date->toDateString(), $end_date->toDateString())
                           ->get();

        return ! $journal_id->isEmpty();
    }

    /**
     * @param float $searched_amount
     * @param string $start_date
     * @param string $end_date
     * @return mixed
     */
    private function getPaidBillFromJournal(int $searched_amount, string $start_date, string $end_date)
    {
        $min_amount = ($searched_amount + 1) * -1;
        $max_amount = ($searched_amount - 1) * -1;

        $query = CorporationWalletJournal::where('corporation_id', $this->corporation_id)
            ->whereIn('ref_type', ['player_donation', 'contract_price_payment_corp'])
            ->whereBetween('date', [$start_date, $end_date])
            ->whereBetween('amount', [$min_amount, $max_amount]);

        return $query;
    }
}
