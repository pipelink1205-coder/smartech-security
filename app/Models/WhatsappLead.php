<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\IpUtils;

class WhatsappLead extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'source',
        'page_url',
        'page_title',
        'ip',
        'user_agent',
        'status',
        'click_count',
    ];

    protected $casts = [
        'click_count' => 'integer',
    ];

    public const SOURCES = [
        'fab' => 'Botón flotante',
        'service_hero' => 'Página de servicio (inicio)',
        'service_sidebar' => 'Página de servicio (cotizar)',
        'service_cta' => 'Página de servicio (final)',
        'contact' => 'Contacto',
        'footer' => 'Pie de página',
        'legal' => 'Página legal',
        'link' => 'Enlace del sitio',
    ];

    public const VISITOR_KINDS = [
        'person' => 'Persona',
        'bot' => 'Bot',
        'unknown' => 'Sin datos',
    ];

    /** @see https://www.cloudflare.com/ips-v4/ */
    private const CLOUDFLARE_CIDRS = [
        '173.245.48.0/20',
        '103.21.244.0/22',
        '103.22.200.0/22',
        '103.31.4.0/22',
        '141.101.64.0/18',
        '108.162.192.0/18',
        '190.93.240.0/20',
        '188.114.96.0/20',
        '197.234.240.0/22',
        '198.41.128.0/17',
        '162.158.0.0/15',
        '104.16.0.0/13',
        '104.24.0.0/14',
        '172.64.0.0/13',
        '131.0.72.0/22',
    ];

    public function getSourceLabelAttribute(): string
    {
        return self::SOURCES[$this->source] ?? $this->source;
    }

    public function visitorKind(): string
    {
        $ua = trim((string) $this->user_agent);

        if ($ua === '') {
            return 'unknown';
        }

        return $this->userAgentLooksLikeBot($ua) ? 'bot' : 'person';
    }

    public function visitorKindLabel(): string
    {
        return self::VISITOR_KINDS[$this->visitorKind()] ?? 'Sin datos';
    }

    public function browserLabel(): string
    {
        $ua = trim((string) $this->user_agent);

        if ($ua === '') {
            return '—';
        }

        $known = [
            'Googlebot' => 'googlebot',
            'Bingbot' => 'bingbot',
            'Ahrefs' => 'ahrefs',
            'Semrush' => 'semrush',
            'Facebook' => 'facebookexternalhit',
            'WhatsApp (vista previa)' => 'whatsapp',
            'GPTBot' => 'gptbot',
            'Instagram' => 'instagram',
            'Facebook App' => 'fbav',
            'Samsung Internet' => 'samsungbrowser',
            'Edge' => 'edg/',
            'Firefox' => 'firefox',
            'Chrome' => 'chrome',
            'Safari' => 'safari',
            'Android' => 'android',
            'iPhone' => 'iphone',
        ];

        $lower = strtolower($ua);

        foreach ($known as $label => $needle) {
            if (str_contains($lower, $needle)) {
                return $label;
            }
        }

        return 'Otro';
    }

    public function ipIsCloudflare(): bool
    {
        return self::ipIsCloudflareAddress((string) $this->ip);
    }

    public static function ipIsCloudflareAddress(?string $ip): bool
    {
        $ip = trim((string) $ip);

        if ($ip === '' || ! filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }

        return IpUtils::checkIp($ip, self::CLOUDFLARE_CIDRS);
    }

    public function userAgentLooksLikeBot(?string $userAgent = null): bool
    {
        $ua = strtolower(trim((string) ($userAgent ?? $this->user_agent)));

        if ($ua === '') {
            return false;
        }

        foreach (self::botUserAgentFragments() as $fragment) {
            if (str_contains($ua, strtolower($fragment))) {
                return true;
            }
        }

        return false;
    }

    public function scopeOfVisitorKind(Builder $query, string $kind): Builder
    {
        return match ($kind) {
            'bot' => $query->whereNotNull('user_agent')->where('user_agent', '!=', '')->where(function (Builder $inner): void {
                foreach (self::botUserAgentFragments() as $index => $fragment) {
                    $method = $index === 0 ? 'where' : 'orWhere';
                    $inner->{$method}('user_agent', 'like', '%'.$fragment.'%');
                }
            }),
            'person' => $query->whereNotNull('user_agent')->where('user_agent', '!=', '')->where(function (Builder $inner): void {
                foreach (self::botUserAgentFragments() as $fragment) {
                    $inner->where('user_agent', 'not like', '%'.$fragment.'%');
                }
            }),
            'unknown' => $query->where(function (Builder $inner): void {
                $inner->whereNull('user_agent')->orWhere('user_agent', '');
            }),
            default => $query,
        };
    }

    /** @return list<string> */
    public static function botUserAgentFragments(): array
    {
        return [
            'googlebot',
            'bingbot',
            'yandex',
            'baiduspider',
            'duckduckbot',
            'slurp',
            'ahrefs',
            'semrush',
            'mj12bot',
            'dotbot',
            'rogerbot',
            'petalbot',
            'facebookexternalhit',
            'facebot',
            'twitterbot',
            'linkedinbot',
            'slackbot',
            'telegrambot',
            'discordbot',
            'pinterestbot',
            'applebot',
            'amazonbot',
            'bytespider',
            'gptbot',
            'claudebot',
            'ccbot',
            'dataforseo',
            'whatsapp',
            'curl/',
            'wget/',
            'python-requests',
            'python-urllib',
            'go-http-client',
            'scrapy',
            'phantomjs',
            'headlesschrome',
            'puppeteer',
            'playwright',
            'crawler',
            'spider',
        ];
    }
}
