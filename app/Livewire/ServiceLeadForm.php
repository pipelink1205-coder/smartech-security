<?php

namespace App\Livewire;

use App\Mail\NewLeadAlert;
use App\Mail\QuoteGenerated;
use App\Models\Quote;
use App\Models\Service;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Throwable;

/**
 * Formulario de contacto contextual de cada página de servicio.
 * El servicio viene fijado por la página y las preguntas salen de config/lead-forms.php.
 */
class ServiceLeadForm extends Component
{
    public string $serviceSlug = '';
    public string $serviceName = '';

    public string $intent = 'info';

    public string $name = '';
    public string $phone = '';
    public string $email = '';
    public string $zone = '';
    /** @var array<string, string> */
    public array $answers = [];
    public string $message = '';
    public string $preferred_visit_date = '';
    public string $preferred_visit_slot = '';
    public bool $consent = false;

    /** Honeypot anti-spam. */
    public string $website = '';

    public bool $submitted = false;

    public function mount(string $serviceSlug, ?string $serviceName = null): void
    {
        $this->serviceSlug = $serviceSlug;
        $this->serviceName = $serviceName
            ?: (Service::query()->where('slug', $serviceSlug)->value('name') ?? ucfirst(str_replace('-', ' ', $serviceSlug)));

        $intent = request()->query('intent');
        if (in_array($intent, ['info', 'visit'], true)) {
            $this->intent = $intent;
        }
    }

    /** Lo disparan los botones "Agendar visita" / "Solicitar información" de la misma página. */
    #[On('setLeadIntent')]
    public function setLeadIntent(string $intent = 'info'): void
    {
        if (in_array($intent, ['info', 'visit'], true)) {
            $this->intent = $intent;
            $this->submitted = false;
        }
    }

    public function setIntent(string $intent): void
    {
        $this->setLeadIntent($intent);
    }

    /** @return array<string, mixed> */
    public static function configFor(string $slug): array
    {
        return config("lead-forms.{$slug}") ?? config('lead-forms.default');
    }

    protected function rules(): array
    {
        $rules = [
            'intent' => ['required', Rule::in(['info', 'visit'])],
            'name' => 'required|min:3|max:100',
            'phone' => ['required', 'max:20', 'regex:/^[0-9+\s().-]{7,20}$/'],
            'email' => 'nullable|email|max:100',
            'zone' => ['nullable', Rule::in(config('site.form_zones', []))],
            'message' => 'nullable|max:1000',
            'consent' => 'accepted',
        ];

        foreach (self::configFor($this->serviceSlug)['questions'] ?? [] as $key => $question) {
            $rules["answers.{$key}"] = ['nullable', Rule::in($question['options'])];
        }

        if ($this->intent === 'visit') {
            $rules['preferred_visit_date'] = 'required|date|after_or_equal:today';
            $rules['preferred_visit_slot'] = ['required', Rule::in(array_keys(Quote::VISIT_SLOTS))];
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.regex' => 'Ingrese un teléfono válido.',
            'preferred_visit_date.required' => 'Indique una fecha preferida.',
            'preferred_visit_date.after_or_equal' => 'La fecha debe ser hoy o posterior.',
            'preferred_visit_slot.required' => 'Seleccione una franja horaria.',
            'consent.accepted' => 'Debe autorizar el tratamiento de datos para que podamos contactarle.',
        ];
    }

    public function submit(): void
    {
        if ($this->website !== '') {
            $this->submitted = true;

            return;
        }

        $key = 'service-lead:'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 6)) {
            $this->addError('name', 'Ha enviado demasiadas solicitudes. Intente de nuevo en unos minutos.');

            return;
        }

        $this->validate();
        RateLimiter::hit($key, 3600);

        $questions = self::configFor($this->serviceSlug)['questions'] ?? [];
        $detail = collect($questions)
            ->map(fn ($q, $k) => ! empty($this->answers[$k]) ? $q['label'].' '.$this->answers[$k] : null)
            ->filter()
            ->values()
            ->when($this->message !== '', fn ($c) => $c->push('Mensaje: '.$this->message))
            ->push('Autorizó tratamiento de datos (Ley 1581): sí, '.now()->format('Y-m-d H:i'))
            ->push('Página de origen: /servicios/'.$this->serviceSlug)
            ->implode("\n");

        $isVisit = $this->intent === 'visit';

        $quote = Quote::create([
            'name' => $this->name,
            'phone' => preg_replace('/\D/', '', $this->phone),
            'email' => $this->email ?: null,
            'service' => $this->serviceName,
            'zone' => $this->zone ?: null,
            'message' => $detail,
            'intent' => $this->intent,
            'preferred_visit_date' => $isVisit ? ($this->preferred_visit_date ?: null) : null,
            'preferred_visit_slot' => $isVisit ? ($this->preferred_visit_slot ?: null) : null,
            'status' => $isVisit ? 'visit_scheduled' : 'new',
        ]);

        try {
            if ($quote->email) {
                Mail::to($quote->email)->queue(new QuoteGenerated($quote));
            }
            Mail::to(config('contact.admin_email'))->queue(new NewLeadAlert($quote));
        } catch (Throwable $e) {
            report($e);
        }

        $this->submitted = true;
        $this->reset(['name', 'phone', 'email', 'zone', 'answers', 'message', 'preferred_visit_date', 'preferred_visit_slot', 'consent']);
    }

    public function render()
    {
        return view('livewire.service-lead-form', [
            'form' => self::configFor($this->serviceSlug),
            'visitSlots' => Quote::VISIT_SLOTS,
            'zones' => config('site.form_zones', []),
        ]);
    }
}
