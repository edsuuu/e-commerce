<?php

declare(strict_types=1);

namespace App\Services\Checkout\Data;

final readonly class CheckoutData
{
    public function __construct(
        public string $customerName,
        public string $customerEmail,
        public ?string $customerPhone,
        public string $shippingZipcode,
        public string $shippingAddress,
        public ?string $shippingNumber,
        public ?string $shippingComplement,
        public ?string $shippingDistrict,
        public string $shippingCity,
        public string $shippingState,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            customerName: self::stringInput($data, 'customer_name'),
            customerEmail: self::stringInput($data, 'customer_email'),
            customerPhone: self::nullableStringInput($data, 'customer_phone'),
            shippingZipcode: self::stringInput($data, 'shipping_zipcode'),
            shippingAddress: self::stringInput($data, 'shipping_address'),
            shippingNumber: self::nullableStringInput($data, 'shipping_number'),
            shippingComplement: self::nullableStringInput($data, 'shipping_complement'),
            shippingDistrict: self::nullableStringInput($data, 'shipping_district'),
            shippingCity: self::stringInput($data, 'shipping_city'),
            shippingState: mb_strtoupper(self::stringInput($data, 'shipping_state')),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toOrderAttributes(): array
    {
        return [
            'customer_name' => $this->customerName,
            'customer_email' => $this->customerEmail,
            'customer_phone' => $this->customerPhone,
            'shipping_zipcode' => $this->shippingZipcode,
            'shipping_address' => $this->shippingAddress,
            'shipping_number' => $this->shippingNumber,
            'shipping_complement' => $this->shippingComplement,
            'shipping_district' => $this->shippingDistrict,
            'shipping_city' => $this->shippingCity,
            'shipping_state' => $this->shippingState,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function stringInput(array $data, string $key): string
    {
        $value = $data[$key] ?? '';

        return is_scalar($value) ? (string) $value : '';
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function nullableStringInput(array $data, string $key): ?string
    {
        $value = self::stringInput($data, $key);

        return $value !== '' ? $value : null;
    }
}
