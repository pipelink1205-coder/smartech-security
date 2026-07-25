<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * Comparador "TI interno vs Smart Tech": costo real de un empleado de planta
 * (salario × factor prestacional) frente al plan mensual de outsourcing.
 * Supuestos en config/quotes.php → it.*
 */
class ItCostComparator extends Component
{
    public string $profile = 'Técnico de soporte';
    public string $plan    = 'demanda';

    public function getProfilesProperty(): array
    {
        return config('quotes.it.salary_profiles', []);
    }

    public function getPlansProperty(): array
    {
        return config('quotes.it.plans', []);
    }

    public function getSalaryProperty(): int
    {
        return (int) ($this->profiles[$this->profile] ?? 0);
    }

    /** Costo mensual real para la empresa: salario + prestaciones y cargas. */
    public function getEmployeeCostProperty(): int
    {
        $factor = (float) config('quotes.it.payroll_factor', 1.53);

        return (int) round($this->salary * $factor, -3);
    }

    public function getPlanCostProperty(): int
    {
        return (int) ($this->plans[$this->plan]['monthly'] ?? 0);
    }

    public function getMonthlySavingsProperty(): int
    {
        return max(0, $this->employeeCost - $this->planCost);
    }

    public function getSavingsPercentProperty(): int
    {
        if ($this->employeeCost <= 0) {
            return 0;
        }

        return (int) round($this->monthlySavings / $this->employeeCost * 100);
    }

    public function render()
    {
        return view('livewire.it-cost-comparator');
    }
}
