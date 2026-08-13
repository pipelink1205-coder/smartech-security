<?php

namespace App\Filament\Pages;

use App\Filament\Resources\DianResolutions\DianResolutionResource;
use App\Services\Dian\DianConfig;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

class DianSettings extends Page
{
    protected static ?string $slug = 'configuracion-dian';

    protected static ?string $title = 'Configuración DIAN';

    protected static ?string $navigationLabel = 'Configuración DIAN';

    protected static string|\UnitEnum|null $navigationGroup = 'Administración';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected Width | string | null $maxContentWidth = Width::SevenExtraLarge;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(app(DianConfig::class)->formState());
    }

    public static function getNavigationBadge(): ?string
    {
        try {
            $pending = collect(app(DianConfig::class)->checklist())
                ->filter(fn (array $item): bool => ! $item['ok'])
                ->count();

            return $pending > 0 ? (string) $pending : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): string | array | null
    {
        return 'warning';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Estado')
                    ->description('Esto no envía facturas. Solo indica qué falta para poder emitir.')
                    ->icon(Heroicon::OutlinedClipboardDocumentCheck)
                    ->compact()
                    ->schema([
                        Html::make(fn (): string => app(DianConfig::class)->statusHtml()),
                    ]),

                Section::make('Envío a la DIAN')
                    ->description('Hay dos llaves: el .env del servidor y este interruptor. Las dos tienen que estar encendidas.')
                    ->icon(Heroicon::OutlinedBolt)
                    ->compact()
                    ->columns(2)
                    ->schema([
                        Toggle::make('dian_enabled')
                            ->label('Habilitar envío a DIAN')
                            ->helperText('No envía nada hasta que DIAN_ENABLED=true esté en el .env del servidor.')
                            ->inline(false),
                        Select::make('dian_environment')
                            ->label('Ambiente')
                            ->options([
                                '2' => 'Habilitación / pruebas (2)',
                                '1' => 'Producción (1)',
                            ])
                            ->required()
                            ->live()
                            ->helperText('Quédate en habilitación hasta que la DIAN apruebe el set de pruebas.'),
                    ]),

                Section::make('Empresa emisora')
                    ->description('Datos del facturador electrónico (Smart Tech), no del cliente.')
                    ->icon(Heroicon::OutlinedBuildingOffice2)
                    ->compact()
                    ->columns(3)
                    ->schema([
                        TextInput::make('dian_company_razon_social')
                            ->label('Razón social')
                            ->maxLength(255)
                            ->columnSpan(2),
                        TextInput::make('dian_company_nombre_comercial')
                            ->label('Nombre comercial')
                            ->maxLength(255),
                        TextInput::make('dian_company_nit')
                            ->label('NIT')
                            ->maxLength(20),
                        TextInput::make('dian_company_dv')
                            ->label('DV')
                            ->maxLength(2),
                        Select::make('dian_company_tipo_documento')
                            ->label('Tipo de documento')
                            ->options([
                                '31' => 'NIT (31)',
                                '13' => 'Cédula (13)',
                            ])
                            ->required(),
                        Select::make('dian_company_tipo_persona')
                            ->label('Tipo de persona')
                            ->options([
                                '1' => 'Jurídica (1)',
                                '2' => 'Natural (2)',
                            ])
                            ->required(),
                        Select::make('dian_company_regimen')
                            ->label('Régimen')
                            ->options([
                                '48' => 'Responsable de IVA (48)',
                                '49' => 'No responsable de IVA (49)',
                            ])
                            ->required(),
                        TextInput::make('dian_company_responsabilidad')
                            ->label('Responsabilidad fiscal')
                            ->maxLength(40)
                            ->helperText('Ej.: O-13, O-47, R-99-PN.'),
                        TextInput::make('dian_company_email')
                            ->label('Correo')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('dian_company_phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(40),
                        TextInput::make('dian_company_address')
                            ->label('Dirección')
                            ->maxLength(255)
                            ->columnSpan(2),
                        TextInput::make('dian_company_municipio_nombre')
                            ->label('Municipio')
                            ->maxLength(80),
                        TextInput::make('dian_company_city_code')
                            ->label('Código ciudad')
                            ->maxLength(10)
                            ->helperText('DIVIPOLA. Medellín = 05001.'),
                        TextInput::make('dian_company_dept_code')
                            ->label('Código depto.')
                            ->maxLength(10)
                            ->helperText('Antioquia = 05.'),
                        TextInput::make('dian_company_country_code')
                            ->label('País')
                            ->maxLength(4)
                            ->default('CO'),
                        TextInput::make('dian_company_actividad_economica')
                            ->label('Actividad económica')
                            ->maxLength(20)
                            ->helperText('Código CIIU.'),
                    ]),

                Section::make('Software y certificado')
                    ->description('ID, PIN y TestSetId salen del registro del software en MUISCA. El .p12 es el certificado ONAC.')
                    ->icon(Heroicon::OutlinedKey)
                    ->compact()
                    ->columns(2)
                    ->schema([
                        TextInput::make('dian_software_id')
                            ->label('Software ID')
                            ->maxLength(80),
                        TextInput::make('dian_software_pin')
                            ->label('Software PIN')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->helperText(fn (): string => app(DianConfig::class)->hasSoftwarePin()
                                ? 'Deja en blanco para no cambiar el PIN ya guardado.'
                                : 'PIN del software en el portal DIAN, no la clave del .p12.'),
                        TextInput::make('dian_test_set_id')
                            ->label('TestSetId')
                            ->maxLength(80)
                            ->visible(fn (Get $get): bool => (string) $get('dian_environment') !== '1')
                            ->helperText('Obligatorio en habilitación. Lo entrega la DIAN al set de pruebas.'),
                        TextInput::make('dian_clave_tecnica')
                            ->label('Clave técnica (respaldo)')
                            ->password()
                            ->revealable()
                            ->maxLength(150)
                            ->helperText('Si la resolución activa tiene clave técnica, se usa esa. Esto es el respaldo.'),
                        FileUpload::make('dian_cert_path')
                            ->label('Certificado .p12 / .pfx')
                            ->disk('local')
                            ->directory('dian/certs')
                            ->visibility('private')
                            ->extraInputAttributes(['accept' => '.p12,.pfx'])
                            ->acceptedFileTypes([
                                'application/x-pkcs12',
                                'application/pkcs12',
                                'application/octet-stream',
                            ])
                            ->maxSize(4096)
                            ->downloadable()
                            ->helperText('Se guarda en storage privado, no en la web pública.')
                            ->columnSpanFull(),
                        TextInput::make('dian_cert_password')
                            ->label('Contraseña del certificado')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->helperText(fn (): string => app(DianConfig::class)->hasCertPassword()
                                ? 'Deja en blanco para no cambiar la contraseña ya guardada.'
                                : 'La clave del archivo .p12, no el PIN del software.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Impuestos')
                    ->icon(Heroicon::OutlinedCalculator)
                    ->compact()
                    ->columns(2)
                    ->schema([
                        TextInput::make('iva_rate')
                            ->label('IVA %')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->helperText('19 para el general.'),
                        TextInput::make('ico_rate')
                            ->label('ICO / INC %')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->helperText('0 si no aplica (seguridad electrónica).'),
                    ]),
            ])
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make($this->getFormActions()),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        app(DianConfig::class)->save($data);

        $this->form->fill(app(DianConfig::class)->formState());

        $config = app(DianConfig::class);
        $missing = $config->missingRequirements();

        if ($config->canEmit()) {
            Notification::make()
                ->title('Configuración DIAN lista')
                ->body('Empresa, software, certificado y resolución están completos. El botón Emitir aparece en la factura.')
                ->success()
                ->send();

            return;
        }

        Notification::make()
            ->title('Configuración DIAN guardada')
            ->body($missing === []
                ? 'Falta encender el envío (.env y/o el interruptor de arriba).'
                : 'Aún falta: '.implode('; ', $missing))
            ->warning()
            ->send();
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Guardar configuración')
                ->submit('save'),
            Action::make('resolutions')
                ->label('Resoluciones DIAN')
                ->color('gray')
                ->url(DianResolutionResource::getUrl('index')),
        ];
    }
}
