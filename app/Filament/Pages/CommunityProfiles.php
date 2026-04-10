<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Domain;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use App\Models\Indicator;
use BackedEnum;
use Illuminate\Support\Facades\Validator;

class CommunityProfiles extends Page
{   

    use InteractsWithForms;

    protected static ?string $title = 'Community Profile Settings';

    protected static ?string $navigationLabel = 'Community Profile Settings';

    protected string $view = 'filament.pages.community-profiles';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-map';

    public ?array $data = [];

    public function mount():void{
        
        $this->form->fill([
            'profile_defaults' => Indicator::where('profile_default', true)
                                    ->get()
                                    ->pluck('id')
                                    ->toArray()
        ]);

    }

    public function save(): void {

        $validator = Validator::make($this->data, [
            'profile_defaults' => 'required|array'
        ]);

        if($validator->fails()){

            Notification::make()
                ->title('Validation Error')
                ->body('Please select at least one indicator as a default profile indicator.')
                ->danger()
                ->send();

            return;
        }

        $profile_defaults = $this->data['profile_defaults'] ?? [];

        // Reset all indicators to not be profile defaults
        Indicator::query()->where('profile_default', true)->update(['profile_default' => false]);


        // Set selected indicators as profile defaults
        Indicator::whereIn('id', $profile_defaults)->update(['profile_default' => true]);

        Notification::make()
            ->title('Success')
            ->body('Saved successfully.')
            ->success()
            ->send();

    }

    public function form($form) {
        
        $tabs = Domain::with(['indicators' => function($query) {
            $query->orderBy('name');
        }])->get()
            ->map(function($domain) {
                return Tab::make($domain->name)
                    ->schema([
                        CheckboxList::make('profile_defaults')
                            ->label('Select Default Indicators')
                            ->options(
                                $domain->indicators->pluck('name', 'id')->toArray()
                            )
                            ->columns(2)
                    ]);
            })->toArray();

        return $form
            ->schema([
                Tabs::make('Domains')->tabs($tabs)
            ])
            ->statePath('data');
        }

}
