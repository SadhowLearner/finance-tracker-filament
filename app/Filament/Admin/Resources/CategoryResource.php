<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
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
                    ->searchable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('notes')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            ->recordClasses(fn(Category $record) => match ($record->type) {
                'income' => 'opacity-100',
                'expense' => 'opacity-50',
                default => null,
            })
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
