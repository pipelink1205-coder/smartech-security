<?php

namespace App\Livewire;

use App\Mail\NewLeadAlert;
use App\Models\Quote;
use App\Support\PropiedadHorizontal as PH;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Throwable;

/**
 * Formulario "Diagnóstico de seguridad gratuito" para unidades residenciales.
 * Guarda el lead como Quote (service = Diagnóstico Unidades) y alerta al admin.
 */
class PhDiagnosticForm extends Component
{
    public const SERVICE_NAME = 'Diagnóstico Unidades Residenciales (PH)';

    public const ROLES = ['Administrador(a)', 'Miembro del consejo', 'Residente', 'Constructora / promotora', 'Otro'];

    public const UNITS = ['Menos de 50', '50 a 150', '151 a 400', 'Más de 400'];

    /** Origen de la página (para saber qué landing convierte). */
    public ?string $origin = '';

    public string $name = '';
    public string $role = '';
    public string $conjunto = '';
    public string $municipio = '';
    public string $units = '';
    /** @var array<int, string> */
    public array $needs = [];
    public string $phone = '';
    public string $email = '';
    public string $message = '';
    public bool $consent = false;

    /** Honeypot anti-spam. */
    public string $website = '';

    public bool $submitted = false;

    public function mount(?string $preServicio = null, ?string $preMunicipio = null, ?string $origin = null): void
    {
        $this->origin = $origin ?: (string) request()->path();

        if ($preServicio && PH::servicio($preServicio)) {
            $this->needs = [$preServicio];
        }

        if ($preMunicipio && PH::municipio($preMunicipio)) {
            $this->municipio = $preMunicipio;
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|min:3|max:100',
            'role' => ['required', Rule::in(self::ROLES)],
            'conjunto' => 'required|min:2|max:120',
            'municipio' => ['required', Rule::in(array_merge(array_keys(PH::cobertura()), ['otro']))],
            'units' => ['nullable', Rule::in(self::UNITS)],
            'needs' => 'array',
            'needs.*' => [Rule::in(array_keys(PH::servicios()))],
            'phone' => ['required', 'max:20', 'regex:/^[0-9+\s().-]{7,20}$/'],
            'email' => 'nullable|email|max:100',
            'message' => 'nullable|max:1000',
            'consent' => 'accepted',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'role.required' => 'Indique su rol en la unidad.',
            'conjunto.required' => 'Escriba el nombre de la unidad o conjunto.',
            'municipio.required' => 'Seleccione el municipio.',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.regex' => 'Ingrese un teléfono válido.',
            'consent.accepted' => 'Debe autorizar el tratamiento de datos para que podamos contactarle.',
        ];
    }

    public function submit(): void
    {
        if ($this->website !== '') {
            $this->submitted = true;

            return;
        }

        $key = 'ph-diagnostic:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('name', 'Ha enviado demasiadas solicitudes. Intente de nuevo en unos minutos.');

            return;
        }

        $this->validate();

        RateLimiter::hit($key, 3600);

        $municipioName = PH::cobertura()[$this->municipio] ?? 'Otro municipio';
        $needs = collect($this->needs)
            ->map(fn (string $slug) => PH::servicio($slug)['name'] ?? $slug)
            ->implode(', ');

        $detail = collect([
            'Rol: '.$this->role,
            $this->units ? 'Apartamentos: '.$this->units : null,
            $needs ? 'Interés: '.$needs : null,
            $this->message ? 'Mensaje: '.$this->message : null,
            'Autorizó tratamiento de datos (Ley 1581): sí, '.now()->format('Y-m-d H:i'),
            'Página de origen: /'.ltrim($this->origin, '/'),
        ])->filter()->implode("\n");

        $quote = Quote::create([
            'name' => $this->name,
            'phone' => preg_replace('/\D/', '', $this->phone),
            'email' => $this->email ?: null,
            'company' => $this->conjunto,
            'zone' => $municipioName,
            'service' => self::SERVICE_NAME,
            'intent' => 'visit',
            'message' => $detail,
        ]);

        try {
            Mail::to(config('contact.admin_email'))->queue(new NewLeadAlert($quote));
        } catch (Throwable $e) {
            report($e);
        }

        $this->submitted = true;
        $this->reset(['name', 'role', 'conjunto', 'units', 'needs', 'phone', 'email', 'message', 'consent']);
    }

    public function render()
    {
        return view('livewire.ph-diagnostic-form', [
            'roles' => self::ROLES,
            'unitOptions' => self::UNITS,
            'municipios' => PH::cobertura(),
            'servicios' => PH::servicios(),
        ]);
    }
}
