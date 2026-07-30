<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Service;

class HomeController extends Controller
{
    public function index()
    {
        $services = Service::active()->onHome()->ordered()->get();
        $iptvService = Service::active()->where('slug', 'iptv-hoteles')->first();

        return view('welcome', compact('services', 'iptvService'));
    }

    public function servicios()
    {
        $services = Service::active()->ordered()->get();

        return view('pages.servicios', compact('services'));
    }

    public function servicioShow(Service $service)
    {
        abort_unless($service->is_active, 404);

        $projects = $service->projects()
            ->with(['images' => fn ($q) => $q->orderByDesc('is_cover')->orderBy('sort_order')])
            ->latest()
            ->limit(6)
            ->get();

        $otherServices = Service::active()
            ->ordered()
            ->whereKeyNot($service->id)
            ->get(['id', 'name', 'slug', 'icon']);

        // Landing dedicada si existe una vista con el slug (ej. outsourcing-ti)
        $customView = "pages.servicios.{$service->slug}";
        if (view()->exists($customView)) {
            return view($customView, compact('service', 'projects', 'otherServices'));
        }

        return view('pages.servicio-show', compact('service', 'projects', 'otherServices'));
    }

    public function proyectos()
    {
        return view('pages.proyectos');
    }

    public function contacto()
    {
        return view('pages.contacto');
    }
}
