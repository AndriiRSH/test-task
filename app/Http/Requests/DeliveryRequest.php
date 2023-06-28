<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryRequest extends FormRequest
{
    public function rules()
    {
        return [
            'customer_name' => 'required|string',
            'phone_number' => 'required|string',
            'email' => 'required|email',
            'delivery_address' => 'required|string',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
            'length' => 'required|numeric',
            'weight' => 'required|numeric',
            'courier' => 'required|string',
        ];
    }
}
