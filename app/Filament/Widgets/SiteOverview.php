<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\Quote;
use App\Models\Service;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SiteOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $newQuotes = Quote::query()->where('status', 'new')->count();

        return [
            Stat::make('Proyectos', Project::query()->count())
                ->description(Project::query()->where('is_featured', true)->count() . ' destacados')
                ->descriptionIcon('heroicon-m-map')
                ->color('success')
                ->url(route('filament.admin.resources.projects.index')),
            Stat::make('Servicios activos', Service::query()->where('is_active', true)->count())
                ->description('Catálogo del sitio')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('primary')
                ->url(route('filament.admin.resources.services.index')),
            Stat::make('Cotizaciones nuevas', $newQuotes)
                ->description($newQuotes ? 'Requieren seguimiento' : 'Sin pendientes')
                ->descriptionIcon('heroicon-m-document-text')
                ->color($newQuotes ? 'danger' : 'gray')
                ->url(route('filament.admin.resources.quotes.index')),
            Stat::make('Usuarios admin', User::query()->count())
                ->description('Acceso al panel')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning')
                ->url(route('filament.admin.resources.users.index')),
        ];
    }
}
