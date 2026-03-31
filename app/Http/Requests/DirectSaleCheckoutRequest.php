<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class DirectSaleCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1'],
            'items.*.device_id' => ['nullable', 'integer', 'exists:devices,id'],
            'items.*.imei' => ['nullable', 'string', 'max:255'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.discount_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.ram_option_id' => ['nullable', 'exists:ram_options,id'],
            'items.*.storage_option_id' => ['nullable', 'exists:storage_options,id'],
            'items.*.color_option_id' => ['nullable', 'exists:color_options,id'],
            'items.*.product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],

            'customer_id' => ['required', 'integer', 'exists:customers,id'],

            'payment_type' => ['required', 'in:cash,installment'],
            'installment_rate_id' => ['nullable', 'integer'],
            'down_payment' => ['nullable', 'numeric', 'min:0'],

            'customer_nrc' => ['nullable', 'string', 'max:255'],
            'customer_address' => ['nullable', 'string'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $items = $this->input('items', []);

            foreach ($items as $i => $item) {
                $hasDevice = !empty($item['device_id']) || !empty($item['imei']);

                if ($hasDevice) {
                    if (isset($item['quantity']) && (int) $item['quantity'] !== 1) {
                        $validator->errors()->add("items.$i.quantity", 'Device lines must have quantity = 1.');
                    }
                } else {
                    if (empty($item['quantity'])) {
                        $validator->errors()->add("items.$i.quantity", 'Quantity is required for non-device lines.');
                    }
                }

                $unit = isset($item['unit_price']) ? (float) $item['unit_price'] : 0.0;
                $discount = isset($item['discount_price']) ? (float) $item['discount_price'] : 0.0;
                if ($discount > $unit) {
                    $validator->errors()->add("items.$i.discount_price", 'Discount cannot exceed unit price.');
                }
            }

            if ($this->input('payment_type') === 'installment') {
                if (empty($this->input('customer_nrc'))) {
                    $validator->errors()->add('customer_nrc', 'NRC is required for installment payments.');
                }
                if (empty($this->input('installment_rate_id'))) {
                    $validator->errors()->add('installment_rate_id', 'Installment plan is required.');
                }
            }
        });
    }
}
