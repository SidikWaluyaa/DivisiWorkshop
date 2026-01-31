<?php

namespace App\Services;

use App\Models\Customer;

class CustomerService
{
    /**
     * Sync customer data based on phone number.
     * Updates existing customer or creates a new one.
     */
    public function syncCustomer(array $data): Customer
    {
        // Map input keys to database columns if necessary, 
        // but typically the controller validation ensures alignment.
        // We'll assume $data contains 'customer_phone', 'customer_name', etc.
        // or standardized keys 'phone', 'name'.
        
        // Let's standardise on passing mapped data or handling the mapping here.
        // Given the Controller uses 'customer_phone' logic, let's accept refined data.
        
        $phone = $data['phone'] ?? $data['customer_phone'];
        
        // Filter out null values to avoid overwriting existing data with nulls if partial update?
        // Reception logic seemed to overwrite. Let's stick to updateOrCreate.
        
        $updateData = [
            'name' => $data['name'] ?? $data['customer_name'],
        ];
        
        // Optional fields
        $optionalMap = [
            'email' => ['email', 'customer_email'],
            'address' => ['address', 'customer_address'],
            'city' => ['city', 'customer_city'],
            'province' => ['province', 'customer_province'],
            'district' => ['district', 'customer_district'],
            'village' => ['village', 'customer_village'],
            'postal_code' => ['postal_code', 'customer_postal_code'],
        ];

        foreach ($optionalMap as $dbKey => $inputKeys) {
            foreach ($inputKeys as $key) {
                if (isset($data[$key])) {
                    $updateData[$dbKey] = $data[$key];
                    break;
                }
            }
        }

        return Customer::updateOrCreate(
            ['phone' => $phone],
            $updateData
        );
    }
}
