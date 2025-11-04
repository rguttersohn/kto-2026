<?php

namespace Tests\Feature;

use App\Models\AssetSchema;
use Tests\TestCase;
use App\Filament\Support\AssetSchemaValidation;
use App\Models\AssetCategory;
use Exception;

class AssetSchemaValidationTest extends TestCase
{

    protected array $schema_rules = [
            'key_1' => 'string',
            'key_2' => 'string', 
            'key_3' => 'numeric'
    ];

    public function test_asset_schema_validation_throws_exception_when_key_missing(){


        $asset_category = AssetCategory::factory()->create();

        $asset_schema = AssetSchema::factory()->create([
            'asset_category_id' => $asset_category->id,
            'schema' => $this->schema_rules 
        ]);

        $key_value_pairs = [
            'key_1' => 'test',
            'key_3' => 10
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("The following key(s) from the schema are missing in this key-value pair: key_2");

        AssetSchemaValidation::validateData($asset_schema, $key_value_pairs);

    }

    public function test_asset_schema_validation_throws_exception_when_key_does_not_exist_in_schema(){


        $asset_category = AssetCategory::factory()->create();

        $asset_schema = AssetSchema::factory()->create([
            'asset_category_id' => $asset_category->id,
            'schema' => $this->schema_rules 
        ]);

        $key_value_pairs = [
            'key_1' => 'test',
            'key_2' => 'test', 
            'key_3' => '23',
            'key_4' => 'test'
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Existing schema does not include the key named 'key_4'");

        AssetSchemaValidation::validateData($asset_schema, $key_value_pairs);

    }

    public function test_asset_schema_validation_throws_exception_when_value_is_wrong_type(){


        $asset_category = AssetCategory::factory()->create();

        $asset_schema = AssetSchema::factory()->create([
            'asset_category_id' => $asset_category->id,
            'schema' => $this->schema_rules 
        ]);

        $key_value_pairs = [
            'key_1' => 'test',
            'key_2' => 'test', 
            'key_3' => 'not a number',
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("key_3 must be numeric");

        AssetSchemaValidation::validateData($asset_schema, $key_value_pairs);

    }

    public function test_asset_schema_validation_returns_void_when_all_key_value_pairs_correct_type(){


        $asset_category = AssetCategory::factory()->create();

        $asset_schema = AssetSchema::factory()->create([
            'asset_category_id' => $asset_category->id,
            'schema' => $this->schema_rules 
        ]);

        $key_value_pairs = [
            'key_1' => 'test',
            'key_2' => 'test', 
            'key_3' => '10',
        ];

        AssetSchemaValidation::validateData($asset_schema, $key_value_pairs);

        $this->assertTrue(true);

    }
}
