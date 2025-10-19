<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\AssetCategory;
use Exception;
use Livewire\Livewire;
use App\Filament\Resources\AssetCategories\Pages\EditAssetCategory;

class EditAssetCategoryTest extends TestCase {


    public function test_non_admin_cannot_publish_asset_category(){

        $user = User::where('role_id', 2)->first();

        if(!$user){

            throw new Exception('User id of 2 not in db. Did you forget to seed users?');
        }

        $this->actingAs($user);

        $asset_category = AssetCategory::withoutGlobalScopes()->first();

        $original_published_status = $asset_category->is_published;

        $asset_category->update([
            'is_published' => !$original_published_status
        ]);

        $asset_category->refresh();

        $this->assertEquals($original_published_status, $asset_category->is_published);

    }


    public function test_is_published_toggle_is_disabled_asset_category_page(){

        $user = User::where('role_id', 2)->first();

        if(!$user){

            throw new Exception('User id of 2 not in db. Did you forget to seed users?');
        }

        $this->actingAs($user);

        $asset_category = AssetCategory::withoutGlobalScopes()->first();

        Livewire::test(EditAssetCategory::class, [
            'record' => $asset_category->getRouteKey()
            ])
            ->assertFormFieldDisabled('is_published');

    }

    public function test_non_admin_cannot_see_delete_button_on_asset_category_page(){

        $user = User::where('role_id', 2)->first();

        if(!$user){

            throw new Exception('User id of 2 not in db. Did you forget to seed users?');
        }

        $this->actingAs($user);

        $asset_category = AssetCategory::withoutGlobalScopes()->first();

        if(!$asset_category){

            throw new Exception('Asset Category table empty');
        }

        Livewire::test(EditAssetCategory::class, [
            'record' => $asset_category->getRouteKey()
            ])
            ->assertActionHidden('delete');
    }
}
