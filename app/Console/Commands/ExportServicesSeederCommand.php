<?php

namespace App\Console\Commands;

use App\Models\Service;
use Illuminate\Console\Command;

class ExportServicesSeederCommand extends Command
{
    protected $signature = 'services:export-seeder
                            {--exclude= : Slugs separados por coma a omitir}
                            {--output= : Ruta del archivo (default: database/seeders/data/services.php)}';

    protected $description = 'Exporta servicios de la BD al archivo del seeder (para versionar lo editado en admin)';

    public function handle(): int
    {
        $exclude = collect(explode(',', (string) $this->option('exclude')))
            ->map(fn ($s) => trim($s))
            ->filter()
            ->all();

        $services = Service::query()
            ->when($exclude, fn ($q) => $q->whereNotIn('slug', $exclude))
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        if ($services->isEmpty()) {
            $this->error('No hay servicios para exportar.');

            return self::FAILURE;
        }

        $output = $this->option('output')
            ?? database_path('seeders/data/services.php');

        $php = $this->buildPhpFile($services);

        file_put_contents($output, $php);

        $this->info("Exportados {$services->count()} servicios → {$output}");
        $this->line('Corre después: php artisan db:seed --class=ServiceSeeder');

        return self::SUCCESS;
    }

    private function buildPhpFile($services): string
    {
        $blocks = [];

        foreach ($services as $service) {
            $blocks[] = $this->exportServiceBlock($service);
        }

        $body = implode(",\n", $blocks);

        return <<<PHP
<?php

/**
 * Servicios del sitio — generado/actualizado desde la BD de desarrollo.
 * Cargar con: php artisan db:seed --class=ServiceSeeder
 * Exportar de nuevo tras editar en admin: php artisan services:export-seeder
 */
return [
{$body}
];

PHP;
    }

    private function exportServiceBlock(Service $service): string
    {
        $lines = [
            '    [',
            "        'name' => {$this->exportString($service->name)},",
            "        'slug' => {$this->exportString($service->slug)},",
            "        'icon' => {$this->exportString($service->icon)},",
            "        'order' => {$service->order},",
            "        'price_from' => {$this->exportPrice($service->price_from)},",
            "        'highlight' => {$this->exportString($service->highlight)},",
            "        'image' => {$this->exportImage($service)},",
            "        'description' => {$this->exportString($service->description)},",
            "        'features' => {$this->exportList($service->features)},",
            "        'long_description' => {$this->exportString($service->long_description)},",
            "        'includes' => {$this->exportList($service->includes)},",
            "        'process_steps' => {$this->exportList($service->process_steps)},",
            "        'brands' => {$this->exportList($service->brands)},",
            "        'standards' => {$this->exportList($service->standards)},",
            "        'tools' => {$this->exportList($service->tools)},",
            "        'faqs' => {$this->exportFaqs($service->faqs)},",
            '    ]',
        ];

        return implode("\n", $lines);
    }

    private function exportImage(Service $service): string
    {
        $slug = $service->slug;
        $image = $service->image;

        if ($slug === 'iptv-hoteles' && $image === config('images.iptv.primary')) {
            return "config('images.iptv.primary')";
        }

        $fromConfig = config("images.services.{$slug}");

        if (is_string($fromConfig) && $fromConfig !== '' && $image === $fromConfig) {
            return "config('images.services.{$slug}')";
        }

        return $this->exportString($image);
    }

    private function exportPrice(mixed $value): string
    {
        if ($value === null || $value === '') {
            return 'null';
        }

        $num = (float) $value;

        return fmod($num, 1.0) === 0.0
            ? (string) (int) $num
            : (string) $num;
    }

    private function exportString(?string $value): string
    {
        if ($value === null) {
            return 'null';
        }

        return var_export($value, true);
    }

    private function exportList(?array $items): string
    {
        $items = array_values(array_filter(
            $items ?? [],
            fn ($item) => is_string($item) ? trim($item) !== '' : $item !== null && $item !== '',
        ));

        if ($items === []) {
            return '[]';
        }

        $lines = array_map(
            fn ($item) => '            '.$this->exportString((string) $item).',',
            $items,
        );

        return "[\n".implode("\n", $lines)."\n        ]";
    }

    private function exportFaqs(?array $faqs): string
    {
        $faqs = array_values(array_filter(
            $faqs ?? [],
            fn ($faq) => is_array($faq) && filled($faq['question'] ?? null),
        ));

        if ($faqs === []) {
            return '[]';
        }

        $blocks = [];

        foreach ($faqs as $faq) {
            $blocks[] = '            ['
                ."'question' => ".$this->exportString((string) ($faq['question'] ?? '')).', '
                ."'answer' => ".$this->exportString((string) ($faq['answer'] ?? '')).'],';
        }

        return "[\n".implode("\n", $blocks)."\n        ]";
    }
}
