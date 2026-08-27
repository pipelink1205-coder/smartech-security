<?php

namespace App\Http\Controllers;

use App\Models\WhatsappLead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WhatsappLeadController extends Controller
{
    public function click(Request $request): RedirectResponse
    {
        $this->record($request, (string) $request->query('from', 'link'), $request->headers->get('referer'));

        return redirect()->away('https://wa.me/'.$this->destinationDigits($request->query('n')));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'source' => 'nullable|string|max:40',
            'page_url' => 'nullable|string|max:2000',
            'page_title' => 'nullable|string|max:180',
        ]);

        $this->record($request, $data['source'] ?? 'link', $data['page_url'] ?? null, $data['page_title'] ?? null);

        return response()->json(['ok' => true]);
    }

    private function record(Request $request, string $source, ?string $pageUrl, ?string $pageTitle = null): void
    {
        if (! isset(WhatsappLead::SOURCES[$source])) {
            $source = 'link';
        }

        WhatsappLead::create([
            'name' => '',
            'phone' => '',
            'source' => $source,
            'page_url' => $this->sanitizePageUrl($request, $pageUrl),
            'page_title' => $pageTitle,
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500) ?: null,
            'status' => 'new',
        ]);
    }

    private function destinationDigits(mixed $number): string
    {
        $digits = preg_replace('/\D/', '', (string) $number);
        $allowed = array_values(array_filter([
            preg_replace('/\D/', '', (string) config('contact.whatsapp')),
            preg_replace('/\D/', '', (string) config('contact.whatsapp_secondary')),
        ]));

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
}
