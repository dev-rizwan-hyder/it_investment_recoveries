<?php

namespace App\Services;

use Square\SquareClient;
use Square\Environments;
use Square\Payments\Requests\CreatePaymentRequest;
use Square\Payments\Requests\GetPaymentRequest;  
use Square\Refunds\Requests\RefundPaymentRequest;
use Square\Types\Money;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class SquarePaymentService
{
    private SquareClient $client;

    public function __construct()
    {
        $environment = env('SQUARE_ENVIRONMENT', 'sandbox');


        $token = env('SQUARE_ACCESS_TOKEN') ?: 'sandbox_placeholder_token';

        $this->client = new SquareClient(
            token: $token,
            options: [
                'baseUrl' => $environment === 'production'
                    ? Environments::Production->value
                    : Environments::Sandbox->value,
            ]
        );
    }

    /**
     * Process payment with Square
     *
     * @param string $sourceId
     * @param float $amount
     * @param string $orderId
     * @param string $idempotencyKey
     * @param array $buyerData
     * @return array
     */
    public function processPayment(
        string $sourceId,
        float $amount,
        string $orderId,
        string $idempotencyKey,
        array $buyerData = []
    ): array {
        try {
            // Validate inputs
            if (empty($sourceId)) {
                throw new \Exception('Payment source ID is required');
            }

            if ($amount <= 0) {
                throw new \Exception('Payment amount must be greater than zero');
            }

            if (empty(env('SQUARE_LOCATION_ID'))) {
                throw new \Exception('Square Location ID not configured');
            }

            // Convert dollars to cents
            $amountInCents = (int) round($amount * 100);

            // Create payment request using CreatePaymentRequest class
            $paymentRequest = new CreatePaymentRequest([
                'sourceId' => $sourceId,
                'amountMoney' => new Money([
                    'amount' => $amountInCents,
                    'currency' => 'USD',
                ]),
                'idempotencyKey' => $idempotencyKey,
                'referenceId' => $orderId,
                'locationId' => env('SQUARE_LOCATION_ID'),
            ]);

            // Create payment using the Square SDK v45.1 API
            $response = $this->client->payments->create($paymentRequest);

            if ($response->getErrors()) {
                $errorMessage = $this->formatErrors($response->getErrors());

                Log::error('Square payment failed', [
                    'order_id' => $orderId,
                    'errors' => $errorMessage,
                ]);

                return [
                    'success' => false,
                    'message' => $errorMessage,
                ];
            }

            $result = $response->getPayment();

            Log::info('Square payment successful', [
                'payment_id' => $result->getId(),
                'order_id' => $orderId,
                'amount' => $amount,
            ]);

            return [
                'success' => true,
                'payment_id' => $result->getId(),
                'status' => $result->getStatus(),
                'amount' => $amount,
                'message' => 'Payment processed successfully',
            ];

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $userMessage = 'Payment processing error: ' . $errorMessage;

            if (str_contains($errorMessage, 'Body: {')) {
                $parts = explode('Body: ', $errorMessage, 2);
                $jsonBody = json_decode($parts[1], true);
                
                if (json_last_error() === JSON_ERROR_NONE && isset($jsonBody['errors'])) {
                    $details = [];
                    foreach ($jsonBody['errors'] as $err) {
                        $code = $err['code'] ?? '';
                        $detail = $err['detail'] ?? '';
                        
                        if (in_array($code, ['PAN_FAILURE', 'GENERIC_DECLINE', 'CVV_FAILURE'])) {
                            if ($this->getEnvironment() === 'production') {
                                $details[] = 'Payment declined. Note: Test cards cannot be used in the Live environment. Please use a real card.';
                            } else {
                                $details[] = 'Payment declined. Please check your card number and try again.';
                            }
                        } else {
                            $details[] = $detail ?: $code;
                        }
                    }
                    
                    if (!empty($details)) {
                        $userMessage = implode(', ', array_unique($details));
                    }
                }
            }

            Log::error('Square payment exception', [
                'order_id' => $orderId,
                'error' => $errorMessage,
            ]);

            return [
                'success' => false,
                'message' => $userMessage,
            ];
        }
    }

    /**
     * Retrieve payment details
     */
    public function getPayment(string $paymentId): ?array
    {
        try {

            $response = $this->client->payments->get($paymentId);

            if ($response->getErrors()) {
                return null;
            }

            $payment = $response->getPayment();

            return [
                'id' => $payment->getId(),
                'status' => $payment->getStatus(),
                'amount' => $payment->getAmountMoney()->getAmount() / 100,
                'currency' => $payment->getAmountMoney()->getCurrency(),
                'created_at' => $payment->getCreatedAt(),
            ];

        } catch (\Exception $e) {

            Log::error('Retrieve payment error: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Refund payment
     */
    public function refundPayment(
        string $paymentId,
        ?float $amount = null
    ): array {
        try {
            // Build refund request using RefundPaymentRequest class
            $refundRequestData = [
                'paymentId' => $paymentId,
                'idempotencyKey' => Uuid::uuid4()->toString(),
            ];

            if ($amount !== null) {
                $refundRequestData['amountMoney'] = new Money([
                    'amount' => (int) round($amount * 100),
                    'currency' => 'USD',
                ]);
            }

            $refundRequest = new RefundPaymentRequest($refundRequestData);
            $response = $this->client->refunds->refundPayment($refundRequest);

            if ($response->getErrors()) {
                return [
                    'success' => false,
                    'message' => $this->formatErrors($response->getErrors()),
                ];
            }

            $refund = $response->getRefund();

            Log::info('Payment refunded successfully', [
                'refund_id' => $refund->getId(),
                'payment_id' => $paymentId,
                'amount' => $amount,
            ]);

            return [
                'success' => true,
                'refund_id' => $refund->getId(),
                'status' => $refund->getStatus(),
                'message' => 'Payment refunded successfully',
            ];

        } catch (\Exception $e) {
            Log::error('Refund payment error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Refund error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Format Square API errors
     */
    private function formatErrors(array $errors): string
    {
        $environment = $this->getEnvironment();

        return implode(', ', array_map(
            function($error) use ($environment) {
                $detail = $error->getDetail() ?? $error->getMessage() ?? 'Unknown error';
                
                if (str_contains($detail, 'PAN_FAILURE') || str_contains($detail, 'GENERIC_DECLINE') || str_contains($detail, 'CVV_FAILURE')) {
                    if ($environment === 'production') {
                        return 'Payment declined. Note: Test cards cannot be used in the Live environment. Please use a real card.';
                    }
                    return 'Payment declined. Please check your card number and try again.';
                }
                
                return $detail;
            },
            $errors
        ));
    }

    /**
     * Get Square Application ID
     */
    public function getApplicationId(): string
    {
        return env('SQUARE_APPLICATION_ID', '');
    }

    /**
     * Get Square Location ID
     */
    public function getLocationId(): string
    {
        return env('SQUARE_LOCATION_ID', '');
    }

    /**
     * Get the Square environment type
     */
    public function getEnvironment(): string
    {
        return env('SQUARE_ENVIRONMENT', 'sandbox');
    }

    /**
     * Get the correct SDK URL based on environment
     */
    public function getSdkUrl(): string
    {
        $environment = $this->getEnvironment();
        return $environment === 'production'
            ? 'https://web.squarecdn.com/v1/square.js'
            : 'https://sandbox.web.squarecdn.com/v1/square.js';
    }

    /**
     * Check if this is a Sandbox configuration
     */
    public function isSandbox(): bool
    {
        return $this->getEnvironment() === 'sandbox';
    }

    /**
     * Validate Sandbox configuration consistency
     */
    public function validateSandboxConfiguration(): array
    {
        $issues = [];
        $appId = $this->getApplicationId();
        $environment = $this->getEnvironment();

        // Check for mismatched configuration
        if ($environment === 'sandbox') {
            // Sandbox should use sandbox-sq0idb- format App ID
            if (!str_starts_with($appId, 'sandbox-sq0idb-')) {
                $issues[] = 'Sandbox environment but App ID does not start with "sandbox-sq0idb-". This will cause authentication failures.';
            }
        } else {
            // Production should NOT start with sandbox- prefix
            if (str_starts_with($appId, 'sandbox-')) {
                $issues[] = 'Production environment but App ID starts with "sandbox-". This will cause authentication failures.';
            }
        }

        return $issues;
    }
}