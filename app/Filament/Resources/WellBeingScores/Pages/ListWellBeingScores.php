<?php

namespace App\Filament\Resources\WellBeingScores\Pages;

use App\Filament\Resources\WellBeingScores\WellBeingScoreResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use App\Models\Domain;

class ListWellBeingScores extends ListRecords
{
    protected static string $resource = WellBeingScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function queryDomainScoresByDomainName(Builder $query, string $domain_name){

        $domain = Domain::where('name', $domain_name)->first();

        return $query->where('domain_id', $domain->id);

    }

    protected function generateTabs():array{

        $domains = Domain::select('id', 'name')->where('is_rankable', true)->get();

        if($domains->isEmpty()){

            return [];
        }

        $tabs = [];
        
        $domains->each(function($domain)use(&$tabs){

            $tabs[Str::slug($domain->name)] = Tab::make($domain->name)
                                                    ->modifyQueryUsing(function(Builder $query)use($domain){
                                                       
                                                        return $this->queryDomainScoresByDomainName($query, $domain->name);

                                                    });

        });

        return $tabs;

    }


    public function getTabs():array{

        return $this->generateTabs();
    }
}
