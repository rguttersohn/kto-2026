<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Indicator;
use Filament\Actions\DeleteAction;


class AdminPanelActionTest extends TestCase
{
   

    public function test_non_admin_cannot_publish_indicator(){

        $user = User::where('role_id', 2)->first();

        $this->actingAs($user);

        $indicator = Indicator::first();

        \Pest\Livewire\livewire(\App\Filament\Resources\PostResource\Pages\EditPost::class, [
            'record' => $indicator->getRouteKey(),
        ])
            ->callAction(DeleteAction::class)
            ->assertForbidden();

    }

}
