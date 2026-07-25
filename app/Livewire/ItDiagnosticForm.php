<?php

namespace App\Livewire;

use App\Mail\NewLeadAlert;
use App\Models\Quote;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Livewire\Component;

/**
 * Formulario de "Diagnóstico gratuito" del servicio Outsourcing de TI.
 * Guarda el lead como Quote (service = Outsourcing de TI) y alerta al admin.
 */
class ItDiagnosticForm extends Component
{
    public const SERVICE_NAME = 'Outsourcing de TI';

    public const EMPLOYEES_RANGES = ['1 a 10', '11 a 50', '51 a 200', 'Más de 200'];

    public const CURRENT_IT_OPTIONS = [
        'No tenemos soporte de TI',
        'Tenemos una persona de TI de planta',
        'Tenemos un proveedor externo',
        'Lo resolvemos informalmente (un conocido, un técnico ocasional)',
        'Otro',
    ];

    public string $name            = '';
    public string $company         = '';
    public string $employees_range = '';
    public string $current_it      = '';
    public string $phone           = '';
    public string $email           = '';
    public string $message         = '';

    /** Honeypot anti-spam: los humanos nunca llenan este campo. */
    public string $website = '';

    public bool $submitted = false;

    protected function rules(): array
    {
        return [
            'name'            => 'required|min:3|max:100',
            'company'         => 'required|min:2|max:120',
            'employees_range' => ['required', Rule::in(self::EMPLOYEES_RANGES)],
            'current_it'      => ['required', Rule::in(self::CURRENT_IT_OPTIONS)],
            'phone'           => ['required', 'max:20', 'regex:/^[0-9+\s().-]{7,20}$/'],
            'email'           => 'nullable|email|max:100',
            'message'         => 'nullable|max:1000',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required'            => 'El nombre es obligatorio.',
            'company.required'         => 'El nombre de la empresa es obligatorio.',
            'employees_range.required' => 'Indique el tamaño de la empresa.',
            'employees_range.in'       => 'Seleccione una opción válida.',
            'current_it.required'      => 'Cuéntenos cómo manejan TI hoy.',
            'current_it.in'            => 'Seleccione una opción válida.',
            'phone.required'           => 'El teléfono es obligatorio.',
            'phone.regex'              => 'Ingrese un teléfono válido.',
        ];
    }

    public function submit(): void
    {
        // Bots que llenan el honeypot: fingir éxito sin guardar nada.
        if ($this->website !== '') {
            $this->submitted = true;
            return;
        }

        $key = 'it-diagnostic:' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('name', 'Ha enviado demasiadas solicitudes. Intente de nuevo en unos minutos.');
            return;
        }

        $this->validate();

        RateLimiter::hit($key, 3600);

        $quote = Quote::create([
            'name'            => $this->name,
            'phone'           => preg_replace('/\D/', '', $this->phone),
            'email'           => $this->email ?: null,
            'company'         => $this->company,
            'employees_range' => $this->employees_range,
            'current_it'      => $this->current_it,
            'service'         => self::SERVICE_NAME,
            'message'         => $this->message ?: null,
        ]);

        Mail::to(config('contact.admin_email'))
            ->queue(new NewLeadAlert($quote));

        $this->submitted = true;
        $this->reset(['name', 'company', 'employees_range', 'current_it', 'phone', 'email', 'message']);
    }

    public function render()
    {
        return view('livewire.it-diagnostic-form', [
            'employeesRanges'  => self::EMPLOYEES_RANGES,
            'currentItOptions' => self::CURRENT_IT_OPTIONS,
        ]);
    }
}
