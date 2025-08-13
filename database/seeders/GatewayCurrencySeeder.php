<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GatewayCurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing records
        DB::table('gateway_currencies')->truncate();

        // Insert gateway currencies data
        $gatewayCurrencies = [
            [
                'id' => 1,
                'name' => 'USD - Stripe',
                'currency' => 'USD',
                'symbol' => '$',
                'method_code' => '101',
                'gateway_alias' => 'stripe',
                'min_amount' => 1.00,
                'max_amount' => 10000.00,
                'percent_charge' => 2.90,
                'fixed_charge' => 0.30,
                'rate' => 1.00,
                'image' => null,
                'gateway_parameter' => json_encode([
                    'public_key' => '',
                    'secret_key' => '',
                    'end_point' => 'https://api.stripe.com/v1/payment_intents'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'USD - PayPal',
                'currency' => 'USD',
                'symbol' => '$',
                'method_code' => '102',
                'gateway_alias' => 'paypal',
                'min_amount' => 1.00,
                'max_amount' => 5000.00,
                'percent_charge' => 3.49,
                'fixed_charge' => 0.49,
                'rate' => 1.00,
                'image' => null,
                'gateway_parameter' => json_encode([
                    'client_id' => '',
                    'client_secret' => '',
                    'app_id' => ''
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'BTC - Bitcoin',
                'currency' => 'BTC',
                'symbol' => '₿',
                'method_code' => '201',
                'gateway_alias' => 'bitcoin',
                'min_amount' => 0.0001,
                'max_amount' => 10.0000,
                'percent_charge' => 1.00,
                'fixed_charge' => 0.00,
                'rate' => 1.00,
                'image' => null,
                'gateway_parameter' => json_encode([
                    'network' => 'mainnet',
                    'confirmations' => 3
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'ETH - Ethereum',
                'currency' => 'ETH',
                'symbol' => 'Ξ',
                'method_code' => '202',
                'gateway_alias' => 'ethereum',
                'min_amount' => 0.001,
                'max_amount' => 100.000,
                'percent_charge' => 1.00,
                'fixed_charge' => 0.00,
                'rate' => 1.00,
                'image' => null,
                'gateway_parameter' => json_encode([
                    'network' => 'mainnet',
                    'confirmations' => 12
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'USDT - Tether (TRC20)',
                'currency' => 'USDT',
                'symbol' => '₮',
                'method_code' => '203',
                'gateway_alias' => 'usdt_trc20',
                'min_amount' => 1.00,
                'max_amount' => 50000.00,
                'percent_charge' => 0.50,
                'fixed_charge' => 1.00,
                'rate' => 1.00,
                'image' => null,
                'gateway_parameter' => json_encode([
                    'network' => 'trc20',
                    'contract_address' => 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t',
                    'confirmations' => 19
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'name' => 'Bank Transfer',
                'currency' => 'USD',
                'symbol' => '$',
                'method_code' => '1001',
                'gateway_alias' => 'bank_transfer',
                'min_amount' => 10.00,
                'max_amount' => 100000.00,
                'percent_charge' => 0.00,
                'fixed_charge' => 5.00,
                'rate' => 1.00,
                'image' => null,
                'gateway_parameter' => json_encode([
                    'account_name' => 'Your Company Name',
                    'account_number' => '1234567890',
                    'bank_name' => 'Bank Name',
                    'routing_number' => '021000021',
                    'swift_code' => 'BANKUS33'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'name' => 'Mobile Money',
                'currency' => 'USD',
                'symbol' => '$',
                'method_code' => '1002',
                'gateway_alias' => 'mobile_money',
                'min_amount' => 1.00,
                'max_amount' => 1000.00,
                'percent_charge' => 2.00,
                'fixed_charge' => 0.25,
                'rate' => 1.00,
                'image' => null,
                'gateway_parameter' => json_encode([
                    'operator' => 'MTN',
                    'country' => 'GH',
                    'instructions' => 'Send money to the provided number and submit transaction reference'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'name' => 'EUR - Euro Bank',
                'currency' => 'EUR',
                'symbol' => '€',
                'method_code' => '1003',
                'gateway_alias' => 'euro_bank',
                'min_amount' => 10.00,
                'max_amount' => 50000.00,
                'percent_charge' => 0.00,
                'fixed_charge' => 3.50,
                'rate' => 0.85,
                'image' => null,
                'gateway_parameter' => json_encode([
                    'iban' => 'DE89370400440532013000',
                    'bic' => 'DEUTDEFF',
                    'bank_name' => 'Deutsche Bank',
                    'account_holder' => 'Your Company Name'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('gateway_currencies')->insert($gatewayCurrencies);

        $this->command->info('Gateway currencies seeded successfully!');
    }
}
