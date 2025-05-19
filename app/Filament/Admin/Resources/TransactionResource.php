<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Source;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Transaction;
use Filament\Support\RawJs;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Admin\Resources\SourceResource;
use App\Filament\Admin\Resources\CategoryResource;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\TransactionResource\Pages;
use App\Filament\Admin\Resources\TransactionResource\RelationManagers;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('IDR'),
                Forms\Components\Select::make('type')
                    ->options([
                        'income' => 'Income',
                        'expense' => 'Expense',
                    ])
                    ->native(false)
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn(callable $set) => $set('category_id', null)),
                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->options(
                        fn($get) =>
                        Category::query()
                            ->when($get('type'), fn($query, $type) => $query->where('type', $type))
                            ->pluck('name', 'id')
                    )
                    ->createOptionForm(
                        function (callable $get): array {
                            $newForm = CategoryResource::getForm();
                            $newForm[1] =
                                Forms\Components\Hidden::make('type')
                                ->default($get('type'));

                            return $newForm;
                        }
                    )
                    ->createOptionUsing(function (array $data): Int {
                        return Category::create($data)->getKey();
                    })
                    ->required()
                    ->searchable()
                    ->placeholder('Select type first')
                    ->disabled(fn($get) => !$get('type'))
                    ->reactive(),
                Forms\Components\Select::make('source_id')
                    ->label('Source')
                    ->options(
                        Source::pluck('name', 'id')
                    )
                    ->createOptionForm(SourceResource::getForm())
                    ->createOptionUsing(function (array $data): Int {
                        return Source::create($data)->getKey();
                    })
                    ->required()
                    ->searchable()
                    ->reactive(),
                // Forms\Components\Select::make('source_id')
                //     ->relationship('source', 'name')
                //     ->required(),
                Forms\Components\FileUpload::make('attachment')
                    ->directory('attachments')
                    ->disk('public')
                    ->columnSpanFull()
                    ->downloadable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable()
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('source.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'income' => 'Income',
                        'expense' => 'Expense',
                    ]),
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('source')
                    ->relationship('source', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
            'view' => Pages\ViewTransaction::route('/{record}/view'),
        ];
    }
}
