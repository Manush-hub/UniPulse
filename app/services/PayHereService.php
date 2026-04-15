<?php

/**
 * PayHereService
 *
 * Generates the checkout fields and hash for PayHere (Sri Lanka's leading
 * payment gateway).  No SDK required — uses a signed HTML form POST.
 *
 * How PayHere works (unlike PayPal, NO server-to-server API call for checkout):
 *  1. We build a form with signed parameters and POST it to PayHere.
 *  2. The customer completes payment on PayHere's hosted page.
 *  3. PayHere sends a server-to-server POST to our notify_url (verifyNotification).
 *  4. PayHere redirects the customer to return_url (success) or cancel_url.
 *
 * Setup checklist:
 *  1. Register at https://www.payhere.lk → Login → Settings → Domains & Credentials
 *  2. Copy your Merchant ID and Merchant Secret into config.php
 *  3. Add your domain (e.g. http://localhost/UniPulse/public) to Allowed Domains
 *  4. Switch PAYHERE_MODE to 'live' and swap credentials for production
 *
 * Sandbox test card:  4111 1111 1111 1111  |  Exp: any future  |  CVV: 123  |  OTP: 123456
 */
class PayHereService
{
    private string $merchantId;
    private string $secret;
    private string $checkoutUrl;

    public function __construct()
    {
        $this->merchantId  = defined('PAYHERE_MERCHANT_ID')  ? PAYHERE_MERCHANT_ID  : '';
        $this->secret      = defined('PAYHERE_SECRET')        ? PAYHERE_SECRET        : '';
        $this->checkoutUrl = defined('PAYHERE_CHECKOUT_URL')  ? PAYHERE_CHECKOUT_URL  : 'https://sandbox.payhere.lk/pay/checkout';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Public API
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Build the signed fields array to POST to PayHere checkout.
     * The controller renders a hidden form and auto-submits it.
     *
     * @param  string $orderId     Unique order identifier (we use timestamp + user_id)
     * @param  float  $amount      Amount in LKR
     * @param  string $returnUrl   Where PayHere redirects on success
     * @param  string $cancelUrl   Where PayHere redirects on cancel
     * @param  string $notifyUrl   Server-to-server notification URL
     * @param  array  $customer    ['first_name', 'last_name', 'email', 'phone']
     * @param  string $items       Item description shown on PayHere page
     * @return array               All fields ready to embed in a form
     */
    public function buildCheckoutFields(
        string $orderId,
        float  $amount,
        string $returnUrl,
        string $cancelUrl,
        string $notifyUrl,
        array  $customer = [],
        string $items    = 'UniPulse Payment'
    ): array {
        $amountFormatted = number_format($amount, 2, '.', '');
        $currency        = 'LKR';

        $hash = $this->generateHash($orderId, $amountFormatted, $currency);

        return [
            'merchant_id'  => $this->merchantId,
            'return_url'   => $returnUrl,
            'cancel_url'   => $cancelUrl,
            'notify_url'   => $notifyUrl,
            'order_id'     => $orderId,
            'items'        => $items,
            'currency'     => $currency,
            'amount'       => $amountFormatted,
            'first_name'   => $customer['first_name'] ?? 'Customer',
            'last_name'    => $customer['last_name']  ?? '',
            'email'        => $customer['email']       ?? '',
            'phone'        => $customer['phone']       ?? '0000000000',
            'address'      => $customer['address']     ?? 'N/A',
            'city'         => $customer['city']        ?? 'Colombo',
            'country'      => 'Sri Lanka',
            'hash'         => $hash,
        ];
    }

    /**
     * Verify a server-to-server notification from PayHere.
     * Call this inside your notify_url handler BEFORE trusting the payment.
     *
     * @param  array $post  The raw $_POST data PayHere sent
     * @return bool         true if the signature is valid
     */
    public function verifyNotification(array $post): bool
    {
        $merchantId    = $post['merchant_id']    ?? '';
        $orderId       = $post['order_id']       ?? '';
        $payhereAmount = $post['payhere_amount'] ?? '';
        $currency      = $post['payhere_currency'] ?? '';
        $statusCode    = $post['status_code']    ?? '';
        $receivedSig   = strtoupper($post['md5sig'] ?? '');

        $localSig = strtoupper(md5(
            $merchantId .
            $orderId .
            $payhereAmount .
            $currency .
            $statusCode .
            strtoupper(md5($this->secret))
        ));

        return hash_equals($localSig, $receivedSig);
    }

    /**
     * Returns the PayHere checkout URL (sandbox or live).
     */
    public function getCheckoutUrl(): string
    {
        return $this->checkoutUrl;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * PayHere hash formula:
     * strtoupper( md5( merchant_id + order_id + amount + currency + strtoupper(md5(secret)) ) )
     */
    private function generateHash(string $orderId, string $amount, string $currency): string
    {
        return strtoupper(md5(
            $this->merchantId .
            $orderId .
            $amount .
            $currency .
            strtoupper(md5($this->secret))
        ));
    }
}
