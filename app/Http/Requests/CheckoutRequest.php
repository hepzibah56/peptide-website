<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:255'],
            'postcode' => ['required', 'string', 'regex:/^[0-9]{4}$/'],
            'city' => ['required', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:120'],
            'org_number' => ['nullable', 'string', 'max:20'],
            'terms' => ['required', 'accepted'],
            'discount_code' => ['nullable', 'string', 'max:30'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Navn er påkrevd.',
            'email.required' => 'E-post er påkrevd.',
            'email.email' => 'Ugyldig e-postadresse.',
            'address.required' => 'Adresse er påkrevd.',
            'postcode.required' => 'Postnummer er påkrevd.',
            'postcode.regex' => 'Postnummer må være fire sifre.',
            'city.required' => 'By er påkrevd.',
            'terms.accepted' => 'Du må godta vilkårene for å fullføre kjøpet.',
        ];
    }
}