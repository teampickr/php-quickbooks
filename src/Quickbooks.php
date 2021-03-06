<?php

namespace PhpQuickbooks;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use PhpQuickbooks\Resources\NameList\Customer;
use PhpQuickbooks\Resources\NameList\Department;
use PhpQuickbooks\Resources\NameList\Item;
use PhpQuickbooks\Resources\NameList\TaxCode;
use PhpQuickbooks\Resources\NameList\Vendor;
use PhpQuickbooks\Resources\Transaction\Invoice;

class Quickbooks
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $company_id;

    /**
     * Quickbooks constructor.
     *
     * @param string $key
     * @param string $secret
     * @param string $token
     * @param string $token_secret
     * @param string $company_id
     * @param string $base_url
     */
    public function __construct(
        string $key,
        string $secret,
        string $token,
        string $token_secret,
        string $company_id,
        string $base_url = 'https://quickbooks.api.intuit.com'
    ) {
        $stack = $this->authenticate($key, $secret, $token, $token_secret);

        $this->initialiseClient($stack, $base_url, $company_id);
    }

    public function authenticate(string $key, string $secret, string $token, string $token_secret)
    {
        $stack = HandlerStack::create();

        $middleware = new Oauth1([
            'consumer_key'    => $key,
            'consumer_secret' => $secret,
            'token'           => $token,
            'token_secret'    => $token_secret,
        ]);

        $stack->push($middleware);

        return $stack;
    }

    public function initialiseClient($stack, string $base_url, string $company_id)
    {
        $this->client = new Client([
            'base_uri' => $base_url . '/v3/company/' . $company_id . '/',
            'timeout'  => 5.0,
            'handler'  => $stack,
            'auth'     => 'oauth',
            'headers'  => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
        ]);
    }

    /**
     * @return \PhpQuickbooks\Resources\NameList\Customer
     */
    public function customer()
    {
        return new Customer($this->client);
    }

    /**
     * @return \PhpQuickbooks\Resources\NameList\Vendor
     */
    public function vendor()
    {
        return new Vendor($this->client);
    }

    /**
     * @return \PhpQuickbooks\Resources\NameList\Department
     */
    public function department()
    {
        return new Department($this->client);
    }

    /**
     * @return \PhpQuickbooks\Resources\NameList\Item
     */
    public function item()
    {
        return new Item($this->client);
    }

    public function taxCode()
    {
        return new TaxCode($this->client);
    }

    /**
     * @return \PhpQuickbooks\Resources\Transaction\Invoice
     */
    public function invoice()
    {
        return new Invoice($this->client);
    }
}
