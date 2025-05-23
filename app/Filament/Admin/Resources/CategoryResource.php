<?php

namespace App\Filament\Admin\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Transaction;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ColorPicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\CategoryResource\Pages;
use App\Filament\Admin\Resources\CategoryResource\RelationManagers;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static function getForm(): array
    {
        return [
            Hidden::make('user_id')
                ->default(Auth::id())
                ->required(),
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            Select::make('type')
                ->options([
                    'income' => 'Income',
                    'expense' => 'Expense',
                ])
                ->required(),
            Textarea::make('notes')
                ->maxLength(65535)
                ->columnSpanFull(),
            Select::make('color')
                ->options([
                    'primary' => 'primary',
                    'success' => 'success',
                    'danger' => 'danger',
                    'warning' => 'warning',
                    'info' => 'info',
                    'gray' => 'gray',
                ])
                ->required(),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->badge()
                    ->searchable()
                    ->color(fn($record) => $record->color),
                IconColumn::make('type')
                    ->icon(fn(string $state): string => match ($state) {
                        'income' => 'heroicon-m-arrow-up-circle',
                        'expense' => 'heroicon-m-arrow-down-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'income' => 'success',
                        'expense' => 'danger',
                    }),
                TextColumn::make('notes')
                    ->searchable()
                    ->toggleable(),
                ColorColumn::make('color')
                    ->getStateUsing(fn($record) => match ($record->color) {
                        'primary' => '#3B82F6',  // Blue
                        'success' => '#10B981',  // Green
                        'danger'  => '#EF4444',  // Red
                        'warning' => '#F59E0B',  // Yellow
                        'info'    => '#0EA5E9',  // Cyan
                        'gray'    => '#6B7280',  // Gray
                        default   => '#9CA3AF',  // Default Gray
                    }),
                TextColumn::make('notes')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters(
                [
                    Tables\Filters\Filter::make('notes')
                        ->query(fn(Builder $query): Builder => $query->whereNotNull('notes'))
                        ->label('Notes')
                        ->translateLabel()
                        ->toggle()
                        ->default()
                        ->indicator('Notes only'),
                    Tables\Filters\SelectFilter::make('type')
                        ->options([
                            'income' => 'Income',
                            'expense' => 'Expense',
                        ])
                        ->attribute('type')
                        ->multiple()
                        ->selectablePlaceholder(false),
                    // ->default(['income', 'expense']),
                    Tables\Filters\SelectFilter::make('transaction')
                        ->relationship('transaction', 'description', fn(Builder $query) => $query->withTrashed())
                        ->searchable()
                        ->preload(),
                    Tables\Filters\Filter::make('created_at')
                        ->form([
                            DatePicker::make('created_from'),
                            DatePicker::make('created_until')
                                ->default(now()),
                        ])
                        ->modifyFormFieldUsing(fn(Toggle $field) => $field->inline(false))
                        ->indicateUsing(function (array $data): ?string {
                            if (empty($data['created_from'] ?? null)) {
                                return null;
                            }
                            return 'Created at ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        })
                        ->query(function (Builder $query, array $data): Builder {
                            return $query
                                ->when(
                                    $data['created_from'] ?? null,
                                    fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                                )
                                ->when(
                                    $data['created_until'] ?? null,
                                    fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                                );
                        })
                ],
                // layout: FiltersLayout::Modal
                // layout: FiltersLayout::AboveContent
                // layout: FiltersLayout::AboveContentCollapsible
                // layout: FiltersLayout::BelowContent
            )
            ->persistFiltersInSession()
            ->deferFilters()
            ->filtersApplyAction(
                fn(Action $action) => $action
                    ->link()
                    ->label('Save filters to table'),
            )
            ->deselectAllRecordsWhenFiltered(false)
            ->filtersTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->label('Filter'),
            )
            ->filtersFormColumns(3)
            ->filtersFormWidth(MaxWidth::FourExtraLarge)
            ->filtersFormMaxHeight('400px')
            // ->hiddenFilterIndicators()
            ->filtersFormSchema(fn(array $filters): array => [
                $filters['notes'],
                Section::make('Visibility')
                    ->description('These filters affect the visibility of the records in the table.')
                    ->schema([
                        $filters['type'],
                        $filters['transaction'],
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                $filters['created_at']
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ReplicateAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            // ->paginated(false)
            ->paginated([8, 10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(25)
            ->queryStringIdentifier('category')
            ->extremePaginationLinks()
            ->recordUrl(
                fn(Category $record): string => route('filament.admin.resources.categories.edit', ['record' => $record]),
            )
            ->openRecordUrlInNewTab()
            ->heading('CATEGORIES')
            ->description('Manage your category here.')
            // ->header(view('test.header_test', [
            //     'heading' => 'Clients',
            //     'description' => 'Manage your clients here.'
            // ]))
            ->poll('10s')
            ->deferLoading()
            ->striped()
            // ->recordClasses(fn(Category $record) => match ($record->type) {
            //     'income' => 'opacity-100',
            //     'expense' => 'opacity-50',
            //     default => null,
            // })
        ;
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
