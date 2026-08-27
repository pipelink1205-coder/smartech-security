<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\WhatsappLead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WhatsappLeadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        if (filled($request->input('website'))) {
            return response()->json([
                'ok' => true,
                'whatsapp_url' => $this->fallbackWhatsappUrl(),
            ]);
        }

        $services = $this->serviceOptions();
        $sources = array_keys(WhatsappLead::SOURCES);

        $data = $request->validate([
            'name' => 'required|string|min:3|max:100',
            'phone' => ['required', 'max:20', 'regex:/^[0-9+\s().-]{7,20}$/'],
            'service' => ['required', 'string', Rule::in($services)],
            'message' => 'nullable|string|max:500',
            'source' => ['nullable', 'string', Rule::in($sources)],
            'page_url' => 'nullable|string|max:500',
            'page_title' => 'nullable|string|max:180',
            'destination' => 'nullable|string|max:20',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.regex' => 'Ingrese un teléfono válido.',
            'service.required' => 'Seleccione el servicio que le interesa.',
            'service.in' => 'Seleccione un servicio válido.',
        ]);

        $phone = WhatsappLead::normalizePhone($data['phone']);
        $destination = $this->sanitizeDestination($data['destination'] ?? null);
        $pageUrl = $this->sanitizePageUrl($request, $data['page_url'] ?? null);

        $existing = WhatsappLead::query()
            ->where('phone', $phone)
            ->latest('id')
            ->first();

        if ($existing) {
            $existing->forceFill([
                'name' => $data['name'],
                'service' => $data['service'],
                'message' => ($data['message'] ?? null) ?: $existing->message,
                'source' => $data['source'] ?? $existing->source,
                'page_url' => $pageUrl ?: $existing->page_url,
                'page_title' => $data['page_title'] ?? $existing->page_title,
                'destination_phone' => $destination,
                'click_count' => $existing->click_count + 1,
            ])->save();

            return response()->json([
                'ok' => true,
                'whatsapp_url' => $existing->fresh()->visitor_whatsapp_url,
            ]);
        }

        $lead = WhatsappLead::create([
            'name' => $data['name'],
            'phone' => $phone,
            'service' => $data['service'],
            'message' => ($data['message'] ?? null) ?: null,
            'source' => $data['source'] ?? 'link',
            'page_url' => $pageUrl,
            'page_title' => $data['page_title'] ?? null,
            'destination_phone' => $destination,
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500) ?: null,
            'status' => 'new',
        ]);

        return response()->json([
            'ok' => true,
            'whatsapp_url' => $lead->visitor_whatsapp_url,
        ]);
    }

    /**
     * @return list<string>
     */
    private function serviceOptions(): array
    {
        $names = Service::active()->ordered()->pluck('name')->all();
        $names[] = 'Varios servicios';
        $names[] = 'No estoy seguro';

        return array_values(array_unique($names));
    }

    private function sanitizeDestination(?string $destination): string
    {
        $digits = preg_replace('/\D/', '', (string) $destination);
        $allowed = WhatsappLead::allowedDestinations();

        if ($digits && in_array($digits, $allowed, true)) {
            return $digits;
        }

        return $allowed[0] ?? (string) config('contact.whatsapp');
    }

    private function sanitizePageUrl(Request $request, ?string $pageUrl): ?string
    {
        $candidate = trim((string) $pageUrl);

        if ($candidate === '') {
            return $request->headers->get('referer');
        }

        $host = parse_url($candidate, PHP_URL_HOST);
        $allowed = array_filter([
            parse_url((string) config('app.url'), PHP_URL_HOST),
            $request->getHost(),
        ]);

        if ($host && ! in_array($host, $allowed, true)) {
            return $request->headers->get('referer');
        }

        return $candidate;
    }

    private function fallbackWhatsappUrl(): string
    {
        return 'https://wa.me/'.config('contact.whatsapp');
    }
}
