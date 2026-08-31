<?php

namespace App\Filament\Resources\SiteContents;

use App\Filament\Resources\SiteContents\Pages\EditSiteContent;
use App\Filament\Resources\SiteContents\Pages\ListSiteContents;
use App\Models\SiteContent;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Resources\ResourceConfiguration;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class SiteContentResource extends Resource
{
    protected static ?string $model = SiteContent::class;

    /** @var class-string<ResourceConfiguration> */
    protected static ?string $configurationClass = ResourceConfiguration::class;

    /**
     * This resource is registered only through the named configurations in
     * AdminPanelProvider, never as one large unscoped Website Content page.
     */
    protected static bool $isDiscovered = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Website Content';

    protected static ?string $modelLabel = 'Website content item';

    protected static ?string $pluralModelLabel = 'Website Content';

    protected static ?string $recordTitleAttribute = 'label';

    /**
     * @return array<string, array{label: string, groups: list<string>, icon: Heroicon, sort: int}>
     */
    public static function sections(): array
    {
        return [
            'hero' => [
                'label' => 'Hero',
                'groups' => ['Homepage / Hero'],
                'icon' => Heroicon::OutlinedRectangleStack,
                'sort' => 10,
            ],
            'about' => [
                'label' => 'About Us',
                'groups' => ['Homepage / About Us'],
                'icon' => Heroicon::OutlinedInformationCircle,
                'sort' => 20,
            ],
            'statistics' => [
                'label' => 'Statistics',
                'groups' => ['Homepage / Statistics'],
                'icon' => Heroicon::OutlinedChartBar,
                'sort' => 30,
            ],
            'articles' => [
                'label' => 'Articles Page',
                'groups' => ['Homepage / Articles', 'Articles Page', 'Article Detail Page'],
                'icon' => Heroicon::OutlinedNewspaper,
                'sort' => 40,
            ],
            'events' => [
                'label' => 'Events Page',
                'groups' => ['Homepage / Events', 'Events Page', 'Event Detail Page'],
                'icon' => Heroicon::OutlinedCalendarDays,
                'sort' => 50,
            ],
            'footer' => [
                'label' => 'Footer',
                'groups' => ['Global / Footer'],
                'icon' => Heroicon::OutlinedRectangleStack,
                'sort' => 60,
            ],
            'contact' => [
                'label' => 'Contact Page',
                'groups' => ['Contact Page'],
                'icon' => Heroicon::OutlinedAtSymbol,
                'sort' => 70,
            ],
            'privacy' => [
                'label' => 'Privacy Page',
                'groups' => ['Privacy Page'],
                'icon' => Heroicon::OutlinedShieldCheck,
                'sort' => 80,
            ],
        ];
    }

    /**
     * @return array{label: string, groups: list<string>, icon: Heroicon, sort: int}|null
     */
    public static function currentSection(): ?array
    {
        $key = static::getConfiguration()?->getKey();

        return is_string($key) ? (static::sections()[$key] ?? null) : null;
    }

    public static function getNavigationLabel(): string
    {
        return static::currentSection()['label'] ?? parent::getNavigationLabel();
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return static::currentSection() ? 'Website Content' : parent::getNavigationGroup();
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return static::currentSection()['icon'] ?? parent::getNavigationIcon();
    }

    public static function getNavigationSort(): ?int
    {
        return static::currentSection()['sort'] ?? parent::getNavigationSort();
    }

    public static function getPluralModelLabel(): string
    {
        return static::currentSection()['label'] ?? parent::getPluralModelLabel();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $section = static::currentSection();

        return $section
            ? $query->whereIn('group', $section['groups'])
            : $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('group')
                    ->label('Content group')
                    ->required()
                    ->maxLength(255)
                    ->disabled()
                    ->helperText('Used only to organize content in the dashboard.'),
                TextInput::make('label')
                    ->label('Admin label')
                    ->required()
                    ->maxLength(255)
                    ->disabled()
                    ->helperText('A readable label for editors; it is never shown on the website.'),
                TextInput::make('key')
                    ->label('Content key')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->disabled()
                    ->helperText('Stable API key. It cannot be changed after creation.'),
                Textarea::make('value_en')
                    ->label('English content')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
                Textarea::make('value_ar')
                    ->label('Arabic content')
                    ->required()
                    ->rows(4)
                    ->extraInputAttributes(['dir' => 'rtl'])
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->label('Optional image')
                    ->image()
                    ->disk('public')
                    ->directory('site-content')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('label')
            ->columns([
                TextColumn::make('group')
                    ->label('Group')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('label')
                    ->label('Content')
                    ->description(fn (SiteContent $record): string => $record->key)
                    ->searchable(),
                TextColumn::make('value_en')
                    ->label('English')
                    ->limit(70)
                    ->toggleable(),
                TextColumn::make('value_ar')
                    ->label('Arabic')
                    ->limit(70)
                    ->toggleable(),
                ImageColumn::make('image')
                    ->label('Image')
                    ->disk('public')
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Last updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSiteContents::route('/'),
            'edit' => EditSiteContent::route('/{record}/edit'),
        ];
    }
}
