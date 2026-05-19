<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SysPaymentTerms extends Model
{
    protected $table = 'sys_payment_terms';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'title', 'active_status', 'payment_schedule',
        'created_by', 'updated_by', 'created_at', 'updated_at',
    ];

    protected $casts = [
        'payment_schedule' => 'array',
    ];

    /**
     * Credit days for due-date logic: max days from payment_schedule, else legacy days_calculation.
     */
    public static function resolveCreditDays($term)
    {
        if (!$term) {
            return 0;
        }

        $schedule = is_array($term) ? ($term['payment_schedule'] ?? null) : ($term->payment_schedule ?? null);

        if (is_string($schedule)) {
            $schedule = json_decode($schedule, true);
        }

        if (is_array($schedule) && count($schedule) > 0) {
            $maxDays = 0;
            foreach ($schedule as $row) {
                $days = (int) (is_array($row) ? ($row['days'] ?? 0) : 0);
                if ($days > $maxDays) {
                    $maxDays = $days;
                }
            }
            return $maxDays;
        }

        if (is_array($term)) {
            return (int) ($term['days_calculation'] ?? 0);
        }

        return (int) ($term->days_calculation ?? 0);
    }

    public static function parseSchedule($term)
    {
        if (!$term) {
            return [];
        }

        $schedule = is_array($term) ? ($term['payment_schedule'] ?? null) : ($term->payment_schedule ?? null);
        if (is_string($schedule)) {
            $schedule = json_decode($schedule, true);
        }
        if (!is_array($schedule)) {
            return [];
        }

        $normalized = [];
        foreach ($schedule as $row) {
            if (!is_array($row)) {
                continue;
            }
            $pct = round((float) ($row['percentage'] ?? 0), 2);
            $days = (int) ($row['days'] ?? 0);
            if ($pct <= 0) {
                continue;
            }
            $normalized[] = [
                'percentage' => $pct,
                'days' => $days,
            ];
        }

        return $normalized;
    }

    public static function maxScheduleSlots($paymentTermsCollection)
    {
        $max = 1;
        foreach ($paymentTermsCollection as $term) {
            $count = count(self::parseSchedule($term));
            if ($count > $max) {
                $max = $count;
            }
        }
        return $max;
    }

    /**
     * Max installment columns from payment terms actually used on sales invoices.
     */
    public static function resolveMaxInstallmentsFromMaps($salesInvoiceMap, $paymentTermsMap)
    {
        $max = 1;
        if (!$salesInvoiceMap) {
            return $max;
        }
        foreach ($salesInvoiceMap as $si) {
            $pt = $paymentTermsMap ? $paymentTermsMap->get($si->payment_terms) : null;
            $count = count(self::parseSchedule($pt));
            if ($count < 1) {
                $count = 1;
            }
            if ($count > $max) {
                $max = $count;
            }
        }
        return $max;
    }

    public static function resolveAsOfDate($asOfDate = null)
    {
        if (!$asOfDate) {
            return Carbon::now()->startOfDay();
        }
        $normalized = SysHelper::normalizeToYmd($asOfDate);
        if ($normalized) {
            return Carbon::parse($normalized)->startOfDay();
        }
        return Carbon::parse($asOfDate)->startOfDay();
    }

    /**
     * Build per-installment due dates, amounts, overdue days, ageing buckets, and finance cost.
     */
    public static function buildOutstandingBreakdown($invoiceDate, $outstandingBalance, $paymentTerm, $financeRatePercent = 0, $asOfDate = null)
    {
        $balance = round((float) $outstandingBalance, 2);
        $asOf = self::resolveAsOfDate($asOfDate);
        $asOfLabel = $asOf->format('d/m/Y');
        $invDate = Carbon::parse($invoiceDate)->startOfDay();
        $schedule = self::parseSchedule($paymentTerm);

        if (empty($schedule)) {
            $legacyDays = self::resolveCreditDays($paymentTerm);
            $schedule = [['percentage' => 100, 'days' => $legacyDays]];
        }

        $title = '';
        if (is_array($paymentTerm)) {
            $title = $paymentTerm['title'] ?? '';
        } elseif ($paymentTerm) {
            $title = $paymentTerm->title ?? '';
        }

        $installments = [];
        $ageing = ['0_30' => 0, '31_60' => 0, '61_90' => 0, '90_plus' => 0];
        $totalFinance = 0;
        $maxOverdue = null;

        foreach ($schedule as $row) {
            $pct = (float) $row['percentage'];
            $days = (int) $row['days'];
            $amount = round($balance * $pct / 100, 2);
            $dueDate = $invDate->copy()->addDays($days);
            $overdueDays = (int) round(($asOf->timestamp - $dueDate->timestamp) / 86400);

            $bucketKey = null;
            if ($overdueDays >= 0 && $overdueDays <= 30) {
                $ageing['0_30'] += $amount;
                $bucketKey = '0_30';
            } elseif ($overdueDays >= 31 && $overdueDays <= 60) {
                $ageing['31_60'] += $amount;
                $bucketKey = '31_60';
            } elseif ($overdueDays >= 61 && $overdueDays <= 90) {
                $ageing['61_90'] += $amount;
                $bucketKey = '61_90';
            } elseif ($overdueDays > 90) {
                $ageing['90_plus'] += $amount;
                $bucketKey = '90_plus';
            }

            $financeCost = self::calculateReceivableFinanceCost($amount, $financeRatePercent, $overdueDays);
            $totalFinance += $financeCost;

            if ($maxOverdue === null || $overdueDays > $maxOverdue) {
                $maxOverdue = $overdueDays;
            }

            $popoverHtml = self::buildInstallmentPopoverHtml($pct, $days, $amount, $dueDate->format('d/m/Y'), $overdueDays, $financeCost, $asOfLabel);

            $installments[] = [
                'percentage' => $pct,
                'days' => $days,
                'label' => rtrim(rtrim(number_format($pct, 2, '.', ''), '0'), '.') . '% / ' . $days . 'd',
                'amount' => $amount,
                'due_date' => $dueDate->format('d/m/Y'),
                'overdue_days' => $overdueDays,
                'ageing_bucket' => $bucketKey,
                'finance_cost' => $financeCost,
                'popover_content_attr' => htmlspecialchars($popoverHtml, ENT_QUOTES, 'UTF-8'),
            ];
        }

        $financePopoverHtml = self::buildFinanceCostPopoverHtml($installments, $totalFinance);

        return [
            'installments' => $installments,
            'ageing' => $ageing,
            'total_finance_cost' => round($totalFinance, 2),
            'finance_cost_popover_content_attr' => htmlspecialchars($financePopoverHtml, ENT_QUOTES, 'UTF-8'),
            'payment_terms_title' => $title,
            'max_overdue_days' => $maxOverdue ?? 0,
            'as_of_label' => $asOfLabel,
        ];
    }

    /**
     * Same formula as inventory ageing: rate% * amount * overdue days / 365 (only when overdue > 0).
     */
    public static function calculateReceivableFinanceCost($amount, $financeRatePercent, $overdueDays)
    {
        $amount = (float) $amount;
        $financeRatePercent = (float) $financeRatePercent;
        $overdueDays = (int) $overdueDays;

        if ($amount <= 0 || $financeRatePercent <= 0 || $overdueDays <= 0) {
            return 0.0;
        }

        return round(($amount * ($financeRatePercent / 100) * $overdueDays) / 365, 2);
    }

    public static function buildInstallmentPopoverHtml($percentage, $days, $amount, $dueDate, $overdueDays, $financeCost, $asOfLabel)
    {
        $esc = function ($v) {
            return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
        };
        $html = '<div class="small text-start ageing-grn-popover text-nowrap">';
        $html .= '<div><strong>As of:</strong> ' . $esc($asOfLabel) . '</div>';
        $html .= '<div><strong>Percentage:</strong> ' . $esc($percentage) . '%</div>';
        $html .= '<div><strong>Days:</strong> ' . $esc($days) . '</div>';
        $html .= '<div><strong>Due date:</strong> ' . $esc($dueDate) . '</div>';
        $html .= '<div><strong>Over due:</strong> ' . $esc($overdueDays) . ' days</div>';
        $html .= '<div><strong>Amount:</strong> ' . $esc(SysHelper::com_curr_format($amount, 2, '.', ',')) . '</div>';
        if ($financeCost > 0) {
            $html .= '<div><strong>Finance cost:</strong> ' . $esc(SysHelper::com_curr_format($financeCost, 2, '.', ',')) . '</div>';
        }
        $html .= '</div>';

        return $html;
    }

    public static function buildFinanceCostPopoverHtml(array $installments, $totalFinanceCost)
    {
        if (empty($installments)) {
            return '';
        }

        $esc = function ($v) {
            return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
        };

        $html = '<div class="small text-start receivable-finance-popover">';
        $html .= '<div class="fw-semibold mb-1">Finance cost breakdown</div>';

        foreach ($installments as $inst) {
            $fc = (float) ($inst['finance_cost'] ?? 0);
            if ($fc <= 0) {
                continue;
            }
            $label = ($inst['due_date'] ?? '') . ' (' . ($inst['label'] ?? '') . ')';
            $html .= '<div class="d-flex justify-content-between gap-3 mb-1">';
            $html .= '<span>' . $esc($label) . '</span>';
            $html .= '<span class="text-nowrap">' . $esc(SysHelper::com_curr_format($fc, 2, '.', ',')) . '</span>';
            $html .= '</div>';
        }

        $html .= '<div class="d-flex justify-content-between gap-3 border-top pt-1 mt-1 fw-semibold">';
        $html .= '<span>Total</span>';
        $html .= '<span class="text-nowrap">' . $esc(SysHelper::com_curr_format((float) $totalFinanceCost, 2, '.', ',')) . '</span>';
        $html .= '</div></div>';

        return $html;
    }

    public static function invoiceMatchesOverdueFilter($installments, $overdueFilter)
    {
        if (empty($installments)) {
            return false;
        }

        $df = 0;
        $dt = 100000;
        if ($overdueFilter === '0') {
            $df = 1;
            $dt = 100000;
        } elseif ($overdueFilter === '30') {
            $df = 0;
            $dt = 30;
        } elseif ($overdueFilter === '60') {
            $df = 31;
            $dt = 60;
        } elseif ($overdueFilter === '90') {
            $df = 61;
            $dt = 90;
        } elseif ($overdueFilter === '90+') {
            $df = 91;
            $dt = 100000;
        }

        foreach ($installments as $inst) {
            $od = (int) $inst['overdue_days'];
            if ($overdueFilter === '0') {
                if ($od > 0) {
                    return true;
                }
            } elseif ($od >= $df && $od <= $dt) {
                return true;
            }
        }

        return false;
    }

    public static function invoiceMatchesAgeingFilter($installments, $ageingFilter)
    {
        if (empty($installments)) {
            return false;
        }

        $df = 0;
        $dt = 30;
        if ($ageingFilter === '0') {
            $df = 0;
            $dt = 30;
        } elseif ($ageingFilter === '30') {
            $df = 31;
            $dt = 60;
        } elseif ($ageingFilter === '60') {
            $df = 61;
            $dt = 90;
        } elseif ($ageingFilter === '90+') {
            $df = 91;
            $dt = 100000;
        }

        foreach ($installments as $inst) {
            $od = (int) $inst['overdue_days'];
            if ($od >= $df && $od <= $dt && ($inst['amount'] ?? 0) > 0) {
                return true;
            }
        }

        return false;
    }
    
    public static function getPaymentTermsName($id){
    	if(!empty($id)){
    		$item = SysPaymentTerms::find($id);
    		return @$item->title;
    	}else{
    		return 'NA';
    	}
    }

    public function createdby(){
		return $this->belongsTo('App\SmStaff', 'created_by', 'id');
    }
    public function updatedby(){
		return $this->belongsTo('App\SmStaff', 'updated_by', 'id');
	}
}
