<?php

namespace App\Http\Controllers;

use App\Models\WhatsappLead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WhatsappLeadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'source' => 'nullable|string|max:40',
            'page_url' => 'nullable|string|max:500',
            'page_title' => 'nullable|string|max:180',
        ]);

        $source = $data['source'] ?? 'link';
        if (! isset(WhatsappLead::SOURCES[$source])) {
            $source = 'link';
        }

        $pageUrl = $this->sanitizePageUrl($request, $data['page_url'] ?? null);

        WhatsappLead::create([
            'name' => '',
            'phone' => '',
            'source' => $source,
            'page_url' => $pageUrl,
            'page_title' => $data['page_title'] ?? null,
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500) ?: null,
            'status' => 'new',
        ]);

        return response()->json(['ok' => true]);
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
