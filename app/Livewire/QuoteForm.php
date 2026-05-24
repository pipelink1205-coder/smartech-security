<?php

namespace App\Livewire;

use App\Models\Quote;
use App\Mail\QuoteGenerated;
use App\Mail\NewLeadAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;

class QuoteForm extends Component
{
    // Campos del formulario
    public string $name    = '';
    public string $phone   = '';
    public string $email   = '';
    public string $service = '';
    public string $zone    = '';
    public string $message = '';

    // Estado
    public bool $submitted   = false;
    public int  $priceMin    = 0;
    public int  $priceMax    = 0;
    public bool $showPreview = false;

    protected function rules(): array
    {
        return [
            'name'    => 'required|min:3|max:100',
            'phone'   => 'required|max:20',
            'email'   => 'nullable|email',
            'service' => 'required',
            'zone'    => 'nullable',
            'message' => 'nullable|max:1000',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required'    => 'El nombre es obligatorio.',
            'name.min'         => 'El nombre debe tener al menos 3 caracteres.',
            'phone.required'   => 'El teléfono es obligatorio.',
            'service.required' => 'Seleccione un servicio.',
        ];
    }

    // Se ejecuta en tiempo real al cambiar el servicio o zona
    public function updatedService(): void
    {
        $this->recalculate();
    }

    public function updatedZone(): void
    {
        $this->recalculate();
    }

    private function recalculate(): void
    {
        if (!$this->service) {
            $this->priceMin = $this->priceMax = 0;
            $this->showPreview = false;
            return;
        }

        $pricing = config('quotes.pricing');
        [$min, $max] = $pricing[$this->service] ?? [0, 0];

        // Aplicar recargo por zona
        $surcharge = config("quotes.zone_surcharge.{$this->zone}", 0) / 100;
        $this->priceMin = (int) ($min * (1 + $surcharge));
        $this->priceMax = (int) ($max * (1 + $surcharge));
        $this->showPreview = $min > 0;
    }

    public function submit(): void
    {
        $this->validate();

        $quote = Quote::create([
            'name'      => $this->name,
            'phone'     => preg_replace('/\D/', '', $this->phone),
            'email'     => $this->email ?: null,
            'service'   => $this->service,
            'zone'      => $this->zone ?: null,
            'message'   => $this->message ?: null,
            'price_min' => $this->priceMin,
            'price_max' => $this->priceMax,
        ]);

        // Emails
        if ($quote->email) {
            Mail::to($quote->email)
                ->queue(new QuoteGenerated($quote));
        }
        Mail::to(config('contact.admin_email'))
            ->queue(new NewLeadAlert($quote));

        $this->submitted = true;
        $this->reset(['name', 'phone', 'email', 'service', 'zone', 'message']);
    }

    public function render()
    {
        return view('livewire.quote-form');
    }
}
