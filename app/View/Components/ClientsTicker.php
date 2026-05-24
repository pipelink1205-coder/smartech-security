<?php

namespace App\View\Components;

use Illuminate\Support\Facades\File;
use Illuminate\View\Component;

class ClientsTicker extends Component
{
    /** @var array<int, array{url: string, name: string}> */
    public array $logos;

    public function __construct()
    {
        $this->logos = $this->discoverLogos();
    }

    /** @return array<int, array{url: string, name: string}> */
    protected function discoverLogos(): array
    {
        $extensions = ['png', 'jpg', 'jpeg', 'webp', 'svg'];
        $seen = [];
        $logos = [];

        foreach (config('site.client_logos_dirs', ['images/clients']) as $relativeDir) {
            $dir = public_path($relativeDir);

            if (! is_dir($dir)) {
                continue;
            }

            foreach (File::files($dir) as $file) {
                if (! in_array(strtolower($file->getExtension()), $extensions, true)) {
                    continue;
                }

                $filename = $file->getFilename();

                if (isset($seen[$filename])) {
                    continue;
                }

                $seen[$filename] = true;
                $logos[] = [
                    'url'  => asset($relativeDir.'/'.$filename),
                    'name' => str_replace(['-', '_'], ' ', pathinfo($filename, PATHINFO_FILENAME)),
                ];
            }
        }

        usort($logos, fn ($a, $b) => strcasecmp($a['name'], $b['name']));

        return $logos;
    }

    public function shouldRender(): bool
    {
        return count($this->logos) > 0;
    }

    public function render()
    {
        return view('components.clients-ticker');
    }
}
