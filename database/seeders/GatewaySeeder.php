<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing records
        DB::table('gateways')->truncate();

        // Insert gateways data
        $gateways = [
            [
                'id' => 1,
                'form_id' => 1,
                'code' => 101,
                'name' => 'Stripe',
                'alias' => 'stripe',
                'status' => 1,
                'gateway_parameters' => json_encode([
                    'public_key' => [
                        'title' => 'Publishable Key',
                        'global' => true,
                        'value' => 'pk_test_...'
                    ],
                    'secret_key' => [
                        'title' => 'Secret Key', 
                        'global' => true,
                        'value' => 'sk_test_...'
                    ],
                    'end_point' => [
                        'title' => 'Endpoint URL',
                        'global' => true,
                        'value' => 'https://api.stripe.com/v1/payment_intents'
                    ]
                ]),
                'supported_currencies' => json_encode(['USD', 'EUR', 'GBP', 'CAD', 'AUD', 'JPY', 'CHF', 'SEK', 'NOK', 'DKK']),
                'crypto' => 0,
                'extra' => json_encode([
                    'webhook_url' => '',
                    'webhook_secret' => '',
                    'capture_method' => 'automatic'
                ]),
                'description' => 'Stripe is a technology company that builds economic infrastructure for the internet. Accept payments online and in person.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'form_id' => 2,
                'code' => 102,
                'name' => 'PayPal',
                'alias' => 'paypal',
                'status' => 1,
                'gateway_parameters' => json_encode([
                    'client_id' => [
                        'title' => 'PayPal Client ID',
                        'global' => true,
                        'value' => ''
                    ],
                    'client_secret' => [
                        'title' => 'PayPal Client Secret',
                        'global' => true,
                        'value' => ''
                    ],
                    'app_id' => [
                        'title' => 'PayPal App ID',
                        'global' => true,
                        'value' => ''
                    ],
                    'mode' => [
                        'title' => 'PayPal Mode',
                        'global' => true,
                        'value' => 'sandbox'
                    ]
                ]),
                'supported_currencies' => json_encode(['USD', 'EUR', 'GBP', 'CAD', 'AUD', 'JPY', 'CHF', 'SEK', 'NOK', 'DKK', 'PLN', 'CZK', 'HUF']),
                'crypto' => 0,
                'extra' => json_encode([
                    'webhook_url' => '',
                    'return_url' => '',
                    'cancel_url' => ''
                ]),
                'description' => 'PayPal is a digital payment platform that allows users to send and receive money securely online.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'form_id' => 3,
                'code' => 103,
                'name' => 'NOWPayments',
                'alias' => 'nowpayments',
                'status' => 1,
                'gateway_parameters' => json_encode([
                    'api_key' => [
                        'title' => 'API Key',
                        'global' => true,
                        'value' => ''
                    ],
                    'ipn_secret' => [
                        'title' => 'IPN Secret',
                        'global' => true,
                        'value' => ''
                    ],
                    'email' => [
                        'title' => 'NOWPayments Email',
                        'global' => true,
                        'value' => ''
                    ],
                    'password' => [
                        'title' => 'NOWPayments Password',
                        'global' => true,
                        'value' => ''
                    ],
                    'environment' => [
                        'title' => 'Environment',
                        'global' => true,
                        'value' => 'sandbox'
                    ]
                ]),
                'supported_currencies' => json_encode(['BTC', 'ETH', 'USDT', 'USDC', 'LTC', 'BCH', 'XRP', 'ADA', 'DOT', 'MATIC', 'TRX', 'BNB', 'DOGE', 'SHIB']),
                'crypto' => 1,
                'extra' => json_encode([
                    'callback_url' => '',
                    'success_url' => '',
                    'cancel_url' => '',
                    'partially_paid_url' => ''
                ]),
                'description' => 'NOWPayments is a crypto payment gateway that lets you accept payments in 300+ cryptocurrencies.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'form_id' => 4,
                'code' => 104,
                'name' => 'CoinPayments',
                'alias' => 'coinpayments',
                'status' => 1,
                'gateway_parameters' => json_encode([
                    'merchant_id' => [
                        'title' => 'Merchant ID',
                        'global' => true,
                        'value' => ''
                    ],
                    'public_key' => [
                        'title' => 'Public Key',
                        'global' => true,
                        'value' => ''
                    ],
                    'private_key' => [
                        'title' => 'Private Key',
                        'global' => true,
                        'value' => ''
                    ],
                    'ipn_secret' => [
                        'title' => 'IPN Secret',
                        'global' => true,
                        'value' => ''
                    ]
                ]),
                'supported_currencies' => json_encode(['BTC', 'ETH', 'LTC', 'BCH', 'DOGE', 'DASH', 'ZEC', 'XMR', 'TRX', 'BNB', 'ADA', 'DOT', 'USDT', 'USDC']),
                'crypto' => 1,
                'extra' => json_encode([
                    'ipn_url' => '',
                    'success_url' => '',
                    'cancel_url' => ''
                ]),
                'description' => 'CoinPayments is a crypto payment processor that supports 1,800+ cryptocurrencies.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'form_id' => 5,
                'code' => 105,
                'name' => 'Coinbase Commerce',
                'alias' => 'coinbase_commerce',
                'status' => 1,
                'gateway_parameters' => json_encode([
                    'api_key' => [
                        'title' => 'API Key',
                        'global' => true,
                        'value' => ''
                    ],
                    'webhook_secret' => [
                        'title' => 'Webhook Secret',
                        'global' => true,
                        'value' => ''
                    ]
                ]),
                'supported_currencies' => json_encode(['BTC', 'ETH', 'LTC', 'BCH', 'USDC', 'DAI']),
                'crypto' => 1,
                'extra' => json_encode([
                    'webhook_url' => '',
                    'success_url' => '',
                    'cancel_url' => ''
                ]),
                'description' => 'Coinbase Commerce allows merchants to accept multiple cryptocurrencies directly into a user-controlled wallet.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'form_id' => 6,
                'code' => 106,
                'name' => 'BitPay',
                'alias' => 'bitpay',
                'status' => 1,
                'gateway_parameters' => json_encode([
                    'token' => [
                        'title' => 'API Token',
                        'global' => true,
                        'value' => ''
                    ],
                    'environment' => [
                        'title' => 'Environment',
                        'global' => true,
                        'value' => 'test'
                    ]
                ]),
                'supported_currencies' => json_encode(['BTC', 'BCH', 'ETH', 'USDC', 'PAX', 'BUSD', 'DOGE', 'LTC', 'WBTC']),
                'crypto' => 1,
                'extra' => json_encode([
                    'notification_url' => '',
                    'redirect_url' => '',
                    'close_url' => ''
                ]),
                'description' => 'BitPay is a bitcoin and cryptocurrency payment service provider.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'form_id' => 7,
                'code' => 107,
                'name' => 'Razorpay',
                'alias' => 'razorpay',
                'status' => 1,
                'gateway_parameters' => json_encode([
                    'key_id' => [
                        'title' => 'Key ID',
                        'global' => true,
                        'value' => ''
                    ],
                    'key_secret' => [
                        'title' => 'Key Secret',
                        'global' => true,
                        'value' => ''
                    ]
                ]),
                'supported_currencies' => json_encode(['INR', 'USD', 'EUR', 'GBP', 'AUD', 'CAD', 'SGD', 'AED', 'MYR']),
                'crypto' => 0,
                'extra' => json_encode([
                    'webhook_secret' => '',
                    'webhook_url' => ''
                ]),
                'description' => 'Razorpay is a payments solution in India that allows businesses to accept, process and disburse payments.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'form_id' => 8,
                'code' => 108,
                'name' => 'Flutterwave',
                'alias' => 'flutterwave',
                'status' => 1,
                'gateway_parameters' => json_encode([
                    'public_key' => [
                        'title' => 'Public Key',
                        'global' => true,
                        'value' => ''
                    ],
                    'secret_key' => [
                        'title' => 'Secret Key',
                        'global' => true,
                        'value' => ''
                    ],
                    'encryption_key' => [
                        'title' => 'Encryption Key',
                        'global' => true,
                        'value' => ''
                    ]
                ]),
                'supported_currencies' => json_encode(['NGN', 'USD', 'EUR', 'GBP', 'UGX', 'KES', 'GHS', 'ZAR', 'XAF', 'XOF']),
                'crypto' => 0,
                'extra' => json_encode([
                    'webhook_url' => '',
                    'redirect_url' => ''
                ]),
                'description' => 'Flutterwave is a payment infrastructure company that provides payment solutions for global merchants and payment service providers.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 9,
                'form_id' => 9,
                'code' => 109,
                'name' => 'Paystack',
                'alias' => 'paystack',
                'status' => 1,
                'gateway_parameters' => json_encode([
                    'public_key' => [
                        'title' => 'Public Key',
                        'global' => true,
                        'value' => ''
                    ],
                    'secret_key' => [
                        'title' => 'Secret Key',
                        'global' => true,
                        'value' => ''
                    ]
                ]),
                'supported_currencies' => json_encode(['NGN', 'USD', 'GHS', 'ZAR', 'KES']),
                'crypto' => 0,
                'extra' => json_encode([
                    'webhook_url' => '',
                    'callback_url' => ''
                ]),
                'description' => 'Paystack is a modern online and offline payment platform for Africa.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 10,
                'form_id' => 10,
                'code' => 110,
                'name' => 'Mollie',
                'alias' => 'mollie',
                'status' => 1,
                'gateway_parameters' => json_encode([
                    'api_key' => [
                        'title' => 'API Key',
                        'global' => true,
                        'value' => ''
                    ]
                ]),
                'supported_currencies' => json_encode(['EUR', 'USD', 'GBP', 'CHF', 'NOK', 'SEK', 'DKK', 'PLN', 'CZK', 'HUF']),
                'crypto' => 0,
                'extra' => json_encode([
                    'webhook_url' => '',
                    'redirect_url' => ''
                ]),
                'description' => 'Mollie is a European payment service provider that offers payment methods for online stores.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 11,
                'form_id' => 11,
                'code' => 201,
                'name' => 'Bitcoin',
                'alias' => 'bitcoin',
                'status' => 1,
                'gateway_parameters' => json_encode([
                    'network' => [
                        'title' => 'Network',
                        'global' => true,
                        'value' => 'mainnet'
                    ],
                    'confirmations' => [
                        'title' => 'Required Confirmations',
                        'global' => true,
                        'value' => '3'
                    ]
                ]),
                'supported_currencies' => json_encode(['BTC']),
                'crypto' => 1,
                'extra' => json_encode([
                    'wallet_address' => '',
                    'private_key' => '',
                    'public_key' => ''
                ]),
                'description' => 'Bitcoin is a decentralized digital currency that can be transferred on the peer-to-peer bitcoin network.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 12,
                'form_id' => 12,
                'code' => 202,
                'name' => 'Ethereum',
                'alias' => 'ethereum',
                'status' => 1,
                'gateway_parameters' => json_encode([
                    'network' => [
                        'title' => 'Network',
                        'global' => true,
                        'value' => 'mainnet'
                    ],
                    'confirmations' => [
                        'title' => 'Required Confirmations',
                        'global' => true,
                        'value' => '12'
                    ]
                ]),
                'supported_currencies' => json_encode(['ETH', 'USDT', 'USDC', 'DAI']),
                'crypto' => 1,
                'extra' => json_encode([
                    'wallet_address' => '',
                    'private_key' => '',
                    'contract_addresses' => []
                ]),
                'description' => 'Ethereum is a decentralized, open-source blockchain with smart contract functionality.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 13,
                'form_id' => 13,
                'code' => 203,
                'name' => 'USDT TRC20',
                'alias' => 'usdt_trc20',
                'status' => 1,
                'gateway_parameters' => json_encode([
                    'network' => [
                        'title' => 'Network',
                        'global' => true,
                        'value' => 'trc20'
                    ],
                    'contract_address' => [
                        'title' => 'Contract Address',
                        'global' => true,
                        'value' => 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t'
                    ],
                    'confirmations' => [
                        'title' => 'Required Confirmations',
                        'global' => true,
                        'value' => '19'
                    ]
                ]),
                'supported_currencies' => json_encode(['USDT']),
                'crypto' => 1,
                'extra' => json_encode([
                    'wallet_address' => '',
                    'private_key' => ''
                ]),
                'description' => 'USDT on TRON network (TRC20) - A stablecoin pegged to the US Dollar.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 14,
                'form_id' => 14,
                'code' => 1001,
                'name' => 'Bank Transfer',
                'alias' => 'bank_transfer',
                'status' => 1,
                'gateway_parameters' => json_encode([
                    'account_name' => [
                        'title' => 'Account Name',
                        'global' => true,
                        'value' => 'Your Company Name'
                    ],
                    'account_number' => [
                        'title' => 'Account Number',
                        'global' => true,
                        'value' => ''
                    ],
                    'bank_name' => [
                        'title' => 'Bank Name',
                        'global' => true,
                        'value' => ''
                    ],
                    'routing_number' => [
                        'title' => 'Routing Number',
                        'global' => true,
                        'value' => ''
                    ],
                    'swift_code' => [
                        'title' => 'SWIFT Code',
                        'global' => true,
                        'value' => ''
                    ]
                ]),
                'supported_currencies' => json_encode(['USD', 'EUR', 'GBP', 'CAD', 'AUD']),
                'crypto' => 0,
                'extra' => json_encode([
                    'processing_time' => '1-3 business days',
                    'instructions' => 'Please provide transaction reference after transfer'
                ]),
                'description' => 'Traditional bank transfer payment method for local and international transfers.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 15,
                'form_id' => 15,
                'code' => 1002,
                'name' => 'Mobile Money',
                'alias' => 'mobile_money',
                'status' => 1,
                'gateway_parameters' => json_encode([
                    'operator' => [
                        'title' => 'Operator',
                        'global' => true,
                        'value' => 'MTN'
                    ],
                    'country' => [
                        'title' => 'Country Code',
                        'global' => true,
                        'value' => 'GH'
                    ],
                    'merchant_number' => [
                        'title' => 'Merchant Number',
                        'global' => true,
                        'value' => ''
                    ]
                ]),
                'supported_currencies' => json_encode(['USD', 'GHS', 'UGX', 'KES', 'TZS', 'RWF']),
                'crypto' => 0,
                'extra' => json_encode([
                    'processing_time' => 'Instant',
                    'instructions' => 'Send money to the provided number and submit transaction reference'
                ]),
                'description' => 'Mobile money payment system popular in Africa for digital transactions.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('gateways')->insert($gateways);

        $this->command->info('Payment gateways seeded successfully!');
    }
}
