<?php

namespace App\Domain\Quotes;

use App\Models\Client;
use App\Models\Quote;

final class ClientSync
{
    /**
     * Vincula la cotización a un cliente del padrón.
     * Reutiliza por documento, correo, teléfono o empresa; si no existe, lo crea.
     */
    public static function ensureForQuote(Quote $quote): ?Client
    {
        if (filled($quote->client_id)) {
            return $quote->client;
        }

        if (blank($quote->name) && blank($quote->company)) {
            return null;
        }

        $client = self::findMatch($quote) ?? Client::create(self::attributesFromQuote($quote));

        $quote->forceFill(['client_id' => $client->id])->saveQuietly();

        return $client;
    }

    public static function findMatch(Quote $quote): ?Client
    {
        if (filled($quote->email)) {
            $byEmail = Client::query()
                ->whereRaw('LOWER(email) = ?', [mb_strtolower(trim((string) $quote->email))])
                ->first();

            if ($byEmail) {
                return $byEmail;
            }
        }

        if (filled($quote->phone)) {
            $byPhone = Client::query()
                ->where('phone', trim((string) $quote->phone))
                ->first();

            if ($byPhone) {
                return $byPhone;
            }
        }

        if (filled($quote->company)) {
            return Client::query()
                ->whereRaw('LOWER(company) = ?', [mb_strtolower(trim((string) $quote->company))])
                ->first();
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    public static function attributesFromQuote(Quote $quote): array
    {
        $company = trim((string) ($quote->company ?: ''));

        return [
            'name' => trim((string) ($quote->name ?: $company ?: 'Cliente')),
            'company' => $company !== '' ? $company : null,
            'email' => filled($quote->email) ? trim((string) $quote->email) : null,
            'phone' => filled($quote->phone) ? trim((string) $quote->phone) : null,
            'address' => filled($quote->client_address) ? trim((string) $quote->client_address) : null,
            'zone' => filled($quote->zone) ? trim((string) $quote->zone) : null,
            'document_type' => filled($company) ? '31' : '13',
            'city_code' => '05001',
            'dept_code' => '05',
            'is_active' => true,
        ];
    }
}
