<?php

namespace App\Filament\Resources\Employees\Schemas;

use App\Models\Employee;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make([
                    'default' => 1,
                    'xl' => 12,
                ])->schema([
                    Group::make()
                        ->schema([
                            self::identificationSection(),
                            self::photoSection(),
                            self::signatureSection(),
                        ])
                        ->columnSpan([
                            'default' => 1,
                            'xl' => 7,
                        ]),

                    Group::make()
                        ->schema([
                            self::cardSection(),
                        ])
                        ->columnSpan([
                            'default' => 1,
                            'xl' => 5,
                        ])
                        ->extraAttributes([
                            'style' => 'position: sticky; top: 1rem; align-self: start;',
                        ]),
                ]),
            ]);
    }

    private static function identificationSection(): Section
    {
        return Section::make('Identificación del empleado')
            ->description('La cédula se guarda cifrada. No se imprime ni aparece en el QR.')
            ->icon('heroicon-o-identification')
            ->schema([
                TextInput::make('employee_code')
                    ->label('Código interno')
                    ->placeholder('Se asigna al guardar')
                    ->disabled()
                    ->dehydrated(false),
                Select::make('status')
                    ->label('Estado')
                    ->options(Employee::STATUSES)
                    ->default('active')
                    ->required(),
                TextInput::make('first_names')
                    ->label('Nombres')
                    ->required()
                    ->maxLength(100)
                    ->live(debounce: 350),
                TextInput::make('last_names')
                    ->label('Apellidos')
                    ->required()
                    ->maxLength(100)
                    ->live(debounce: 350),
                Select::make('document_type')
                    ->label('Tipo de documento')
                    ->options(['CC' => 'Cédula de ciudadanía', 'CE' => 'Cédula de extranjería', 'PPT' => 'PPT'])
                    ->default('CC')
                    ->required(),
                TextInput::make('document_number')
                    ->label('Número de documento')
                    ->password()
                    ->revealable()
                    ->maxLength(30),
                TextInput::make('position')
                    ->label('Cargo')
                    ->required()
                    ->maxLength(120)
                    ->live(debounce: 350),
                TextInput::make('area')
                    ->label('Área')
                    ->maxLength(120),
                DatePicker::make('started_at')
                    ->label('Fecha de ingreso'),
                TextInput::make('email')
                    ->label('Correo corporativo')
                    ->email()
                    ->maxLength(160),
                TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel()
                    ->maxLength(40),
                Textarea::make('notes')
                    ->label('Notas internas')
                    ->rows(2)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    private static function photoSection(): Section
    {
        return Section::make('Fotografías')
            ->description('Dos fotos: una para quien escanea el QR y otra para el carnet. El recorte se hace al guardar.')
            ->icon('heroicon-o-photo')
            ->schema([
                FileUpload::make('photo_original')
                    ->label('1. Foto pública (QR)')
                    ->disk('local')
                    ->directory('employees/originals')
                    ->visibility('private')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(10240)
                    ->helperText('Foto de frente, con fondo. Es la que ve un cliente o portería.'),
                FileUpload::make('photo_card')
                    ->label('2. Foto del carnet')
                    ->disk('local')
                    ->directory('employees/card-sources')
                    ->visibility('private')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(10240)
                    ->helperText('JPG con fondo o PNG ya recortado. Se ajusta al guardar.')
                    ->live(),
                Section::make('Recorte listo (opcional)')
                    ->description('Solo si ya tienes un PNG recortado y quieres usarlo en lugar del automático.')
                    ->compact()
                    ->collapsed()
                    ->schema([
                        FileUpload::make('photo_cutout')
                            ->label('PNG recortado')
                            ->disk('local')
                            ->directory('employees/cutouts')
                            ->visibility('private')
                            ->image()
                            ->acceptedFileTypes(['image/png', 'image/webp'])
                            ->live(),
                    ])
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    private static function signatureSection(): Section
    {
        return Section::make('Firma')
            ->description('Cada empleado guarda su firma para informes, visitas y constancias. En el carnet solo se imprime la del representante legal.')
            ->icon('heroicon-o-pencil-square')
            ->schema([
                Toggle::make('is_legal_representative')
                    ->label('Este empleado es el representante legal')
                    ->helperText('Al marcar, su firma se imprime en todos los carnets y se quita el cargo a cualquier otra persona. Si nadie está marcado, el reverso sale sin firma.')
                    ->inline(false)
                    ->live()
                    ->columnSpanFull(),
                Hidden::make('signature_drawn')->dehydrated(),
                View::make('filament.forms.employee-signature-pad'),
                FileUpload::make('authorized_signature')
                    ->label('O cargar la firma desde el computador')
                    ->disk('local')
                    ->directory('employees/signatures')
                    ->visibility('private')
                    ->image()
                    ->acceptedFileTypes(['image/png', 'image/webp', 'image/jpeg'])
                    ->maxSize(4096)
                    ->helperText('Si la foto tiene hoja o fondo, al guardar se deja solo el trazo. Si dibujas y también cargas archivo, se guarda el dibujo.')
                    ->live(),
            ]);
    }

    private static function cardSection(): Section
    {
        return Section::make('Carnet')
            ->description('Ajusta la foto con los controles. Guarda para recortar el fondo y después descarga el PDF.')
            ->icon('heroicon-o-eye')
            ->compact()
            ->headerActions([
                Action::make('resetFraming')
                    ->label('Centrar foto')
                    ->link()
                    ->action(function (Set $set): void {
                        $set('portrait_scale', 88);
                        $set('portrait_x', 4);
                        $set('portrait_y', 2);
                    }),
            ])
            ->schema([
                View::make('filament.forms.employee-card-preview'),

                Grid::make(3)
                    ->schema([
                        Slider::make('portrait_scale')
                            ->label('Acercar')
                            ->range(50, 150)
                            ->step(1)
                            ->default(88)
                            ->tooltips()
                            ->live(debounce: 80),
                        Slider::make('portrait_x')
                            ->label('Horizontal')
                            ->range(-40, 40)
                            ->step(1)
                            ->default(4)
                            ->tooltips()
                            ->live(debounce: 80),
                        Slider::make('portrait_y')
                            ->label('Vertical')
                            ->range(-40, 40)
                            ->step(1)
                            ->default(2)
                            ->tooltips()
                            ->live(debounce: 80),
                    ]),

                View::make('filament.forms.employee-card-actions'),
            ]);
    }
}
