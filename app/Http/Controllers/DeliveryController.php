<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\DeliveryRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;

class DeliveryController extends Controller
{
    public function delivery(DeliveryRequest $request)
    {
        $validatedData = $request->validated();

        // Отримання даних з валідованих вхідних даних
        $customerName = $validatedData['customer_name'];
        $phoneNumber = $validatedData['phone_number'];
        $email = $validatedData['email'];
        $senderAddress = config('app.sender_address');
        $deliveryAddress = $validatedData['delivery_address'];
        $parcelData = Arr::only($validatedData, ['width', 'height', 'length', 'weight']);
        $courier = $validatedData['courier'];

        //якщо курєрок більше
        $courierEndpoints = [
            'nova_poshta' => 'https://novaposhta.test/api/delivery',
            'ukrposhta' => 'https://ukrposhta.example/api/delivery',
            'justin' => 'https://justin.example/api/delivery',
        ];

        if (!array_key_exists($courier, $courierEndpoints)) {
            return response()->json(['message' => 'Unsupported courier selected'], 400);
        }

        try {
            $response = Http::post($courierEndpoints[$courier],[
                'customer_name' => $customerName,
                'phone_number' => $phoneNumber,
                'email' => $email,
                'sender_address' => $senderAddress,
                'delivery_address' => $deliveryAddress,
                'parcel_data' => $parcelData,
            ]);

            // Перевірка статусу відповіді
            if ($response->ok()) {
                return response()->json(['message' => 'Delivery created successfully']);
            } else {
                // Обробка помилок
                if ($response->status() == 500) {
                    return response()->json(['message' => 'Failed to create delivery'], $response->status());
                } else {
                    return response()->json(['message' => 'Issue with the delivery service. Please try again later.'], 500);
                }
            }
        } catch (\Exception $e) {
            // Обробка помилки від кур'єрської служби
            return response()->json(['message' => 'Failed to create delivery'], 500);
        }
    }
}
