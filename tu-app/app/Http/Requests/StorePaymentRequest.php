<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['System Admin', 'Bendahara', 'Petugas Transaksi']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'student_id' => [
                'required',
                'integer',
                'exists:students,id',
            ],
            'payment_date' => [
                'required',
                'date',
                'before_or_equal:today',  // Can't be in the future
                'after_or_equal:' . now()->subYear()->toDateString(),  // Not older than 1 year
            ],
            'payment_method' => [
                'required',
                'in:cash,transfer',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:500',
            ],
            'items' => [
                'required',
                'array',
                'min:1',
            ],
            'items.*.fee_id' => [
                'required',
                'integer',
                'exists:fees,id',
            ],
            'items.*.amount' => [
                'required',
                'numeric',
                'min:0',
                'max:100000000',  // Max 100 million per item
            ],
            'items.*.period_month' => [
                'nullable',
                'integer',
                'min:1',
                'max:12',
            ],
            'items.*.period_year' => [
                'nullable',
                'integer',
                'min:2020',
                'max:2100',
            ],
        ];
    }

    /**
     * Get custom attribute names for error messages.
     */
    public function attributes(): array
    {
        return [
            'student_id' => 'Siswa',
            'payment_date' => 'Tanggal Pembayaran',
            'payment_method' => 'Metode Pembayaran',
            'notes' => 'Catatan',
            'items' => 'Item Pembayaran',
            'items.*.fee_id' => 'Jenis Biaya',
            'items.*.amount' => 'Jumlah',
            'items.*.period_month' => 'Bulan',
            'items.*.period_year' => 'Tahun',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'payment_date.before_or_equal' => 'Tanggal pembayaran tidak boleh di masa depan.',
            'payment_date.after_or_equal' => 'Tanggal pembayaran tidak boleh lebih dari 1 tahun yang lalu.',
            'items.min' => 'Minimal harus ada 1 item pembayaran.',
            'items.*.amount.max' => 'Jumlah maksimal per item adalah Rp 100.000.000.',
            'items.*.amount.min' => 'Jumlah tidak boleh negatif.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize notes
        if ($this->has('notes') && $this->notes) {
            $this->merge([
                'notes' => strip_tags($this->notes),
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check that at least one item has amount > 0
            $items = $this->input('items', []);
            $hasValidAmount = collect($items)->contains(fn($item) => isset($item['amount']) && $item['amount'] > 0);

            if (!$hasValidAmount) {
                $validator->errors()->add('items', 'Minimal satu item pembayaran harus memiliki jumlah lebih dari 0.');
            }
        });
    }
}
