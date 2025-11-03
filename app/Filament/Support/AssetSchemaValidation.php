<?php

namespace App\Filament\Support;

use App\Models\AssetSchema;
use Exception;
use Illuminate\Support\Facades\Log;


class AssetSchemaValidation {
    
    protected static array $allowed_types = ['numeric', 'string'];
    
    public static function validateData(AssetSchema $asset_schema, array $key_value_pairs): bool | Exception{
        
        if(!$asset_schema){

            return true;
        }

        $schema_rules = $asset_schema->schema;

        $schema_difference = array_diff(array_keys($schema_rules), array_keys($key_value_pairs));

        if($schema_difference){

            $schema_difference_label = implode(", ", $schema_difference);

            return new Exception("The following key(s) from the schema are missing in this key-value pair: $schema_difference_label");

        }
        
        foreach($key_value_pairs as $key=>$value){

            if(!isset($schema_rules[$key])){

                return new Exception("Existing schema does not include the key named '$key'");
                
            }
                                
            $expected_type = $schema_rules[$key] ?? null;

            if(!$expected_type){

                continue;
            }

            $validator = validator(
                ['value' => $value],
                ['value' => $expected_type]
            );

            if ($validator->fails()) {
                
                return new Exception("$key must be $expected_type");

            }

        }

        return true;

    }


    public static function validateSchema(array $key_value_pairs): bool | Exception{
        
        foreach ($key_value_pairs as $key_value) {
            
            if(!in_array($key_value['value'], static::$allowed_types)){

                $allowed_types_label = implode(", ", static::$allowed_types);

                return new Exception("The value must be one of $allowed_types_label");

            }
        }

        return true;

    }
}