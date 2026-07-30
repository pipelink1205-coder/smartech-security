<?php

namespace App\Livewire;

use App\Models\Quote;
use App\Models\Service;
use App\Mail\QuoteGenerated;
use App\Mail\NewLeadAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class QuoteForm extends Component
{
    public string $intent = 'info';

    public string $name    = '';
    public string $phone   = '';
    public string $email   = '';
    public string $service = '';
    public string $zone    = '';
    public string $message = '';

    public string $preferred_visit_date = '';
    public string $preferred_visit_slot = '';

    public bool $submitted = false;

    public function mount(?string $intent = null): void
    {
        $fromQuery = $intent ?: request()->query('intent');

        if (in_array($fromQuery, ['info', 'visit'], true)) {
            $this->intent = $fromQuery;
        }
    }

    #[On('setQuoteIntent')]
    public function setQuoteIntent(string $intent = 'info'): void
    {
        if (in_array($intent, ['info', 'visit'], true)) {
            $this->intent = $intent;
            $this->submitted = false;
        }
    }

    public function setIntent(string $intent): void
    {
        $this->setQuoteIntent($intent);
    }

    protected function rules(): array
    {
        $services = $this->serviceOptions();
        $zones = config('site.form_zones', []);

        $rules = [
            'intent'  => ['required', Rule::in(['info', 'visit'])],
            'name'    => 'required|min:3|max:100',
            'phone'   => ['required', 'max:20', 'regex:/^[0-9+\s().-]{7,20}$/'],
            'email'   => 'nullable|email|max:100',
            'service' => ['required', Rule::in($services)],
            'zone'    => ['nullable', Rule::in($zones)],
            'message' => 'nullable|max:1000',
            'preferred_visit_date' => 'nullable|date',
            'preferred_visit_slot' => ['nullable', Rule::in(array_keys(Quote::VISIT_SLOTS))],
        ];

        if ($this->intent === 'visit') {
            $rules['preferred_visit_date'] = 'required|date|after_or_equal:today';
            $rules['preferred_visit_slot'] = ['required', Rule::in(array_keys(Quote::VISIT_SLOTS))];
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'name.required'    => 'El nombre es obligatorio.',
            'name.min'         => 'El nombre debe tener al menos 3 caracteres.',
            'phone.required'   => 'El teléfono es obligatorio.',
            'phone.regex'      => 'Ingrese un teléfono válido.',
            'service.required' => 'Seleccione un servicio.',
            'service.in'       => 'Seleccione un servicio válido.',
            'zone.in'          => 'Seleccione una zona válida.',
            'preferred_visit_date.required' => 'Indique una fecha preferida.',
            'preferred_visit_date.after_or_equal' => 'La fecha debe ser hoy o posterior.',
            'preferred_visit_slot.required' => 'Seleccione una franja horaria.',
        ];
    }

    public function submit(): void
    {
        $this->validate();

        $isVisit = $this->intent === 'visit';

        $quote = Quote::create([
            'name'    => $this->name,
            'phone'   => preg_replace('/\D/', '', $this->phone),
            'email'   => $this->email ?: null,
            'service' => $this->service,
            'zone'    => $this->zone ?: null,
            'message' => $this->message ?: null,
            'intent'  => $this->intent,
            'preferred_visit_date' => $isVisit ? ($this->preferred_visit_date ?: null) : null,
            'preferred_visit_slot' => $isVisit ? ($this->preferred_visit_slot ?: null) : null,
            'status'  => $isVisit ? 'visit_scheduled' : 'new',
        ]);

        if ($quote->email) {
            Mail::to($quote->email)->queue(new QuoteGenerated($quote));
        }
        Mail::to(config('contact.admin_email'))->queue(new NewLeadAlert($quote));

        $this->submitted = true;
        $this->reset([
            'name', 'phone', 'email', 'service', 'zone', 'message',
            'preferred_visit_date', 'preferred_visit_slot',
        ]);
    }

    /**
     * @return list<string>
     */
    private function serviceOptions(): array
    {
        $names = Service::active()->ordered()->pluck('name')->all();
        $names[] = 'Varios servicios';

        return array_values(array_unique($names));
    }

    public function render()
    {
        return view('livewire.quote-form', [
            'serviceOptions' => $this->serviceOptions(),
            'visitSlots' => Quote::VISIT_SLOTS,
        ]);
    }
}
