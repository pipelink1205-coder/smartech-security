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

    public function proyectos()
    {
        return view('pages.proyectos');
    }

    public function contacto()
    {
        return view('pages.contacto');
    }
}
