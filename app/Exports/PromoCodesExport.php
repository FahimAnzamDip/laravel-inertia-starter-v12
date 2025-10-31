<?php

namespace App\Exports;

use App\Models\PromoCode;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PromoCodesExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected $couponId;

    protected $filters;

    public function __construct($couponId, $filters = [])
    {
        $this->couponId = $couponId;
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = PromoCode::where('coupon_id', $this->couponId)
            ->with(['coupon']);

        // Apply search filter
        if (! empty($this->filters['search'])) {
            $query->where('code', 'like', '%'.$this->filters['search'].'%');
        }

        // Apply status filter
        if (! empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Promo Code',
            'Status',
            'Usage Count',
            'Valid From',
            'Valid Until',
            'Created At',
        ];
    }

    /**
     * @param  PromoCode  $promoCode
     */
    public function map($promoCode): array
    {
        return [
            $promoCode->code,
            $promoCode->status,
            $promoCode->usage_count,
            Carbon::parse($promoCode->coupon->valid_from)->format('Y-m-d h:i A'),
            Carbon::parse($promoCode->coupon->valid_until)->format('Y-m-d h:i A'),
            $promoCode->created_at->format('Y-m-d h:i A'),
        ];
    }

    /**
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
        ];
    }
}
