<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Service;

class HomeController extends Controller
{
    public function index()
    {
        $services  = Service::active()->ordered()->get();
        $projects  = Project::featured()->latest()->take(6)->get();

        return view('welcome', compact('services', 'projects'));
    }

    public function servicios()
    {
        $services = Service::active()->ordered()->get();
        return view('pages.servicios', compact('services'));
    }

    public function proyectos()
    {
        $projects = Project::latest()->paginate(9);
        return view('pages.proyectos', compact('projects'));
    }

    public function contacto()
    {
        return view('pages.contacto');
    }
}
