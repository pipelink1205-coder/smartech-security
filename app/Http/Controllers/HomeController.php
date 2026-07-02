<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Service;

class HomeController extends Controller
{
    public function index()
    {
        $services  = Service::active()->ordered()->get();
        return view('welcome', compact('services'));
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
            ->limit(12)
            ->get();

        return view('pages.servicio-show', compact('service', 'projects'));
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
