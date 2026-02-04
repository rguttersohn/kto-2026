<?php

namespace App\Filament\Support;

use App\Models\AssetSchema;
use Exception;


class AssetSchemaValidation {
    
    protected static array $allowed_types = ['numeric', 'string'];
    
    public static function validateData(AssetSchema | null $asset_schema, array $key_value_pairs) {
        
        if(!$asset_schema){

            return;
        }

        $schema_rules = $asset_schema->schema;

        $schema_difference = array_diff(array_keys($schema_rules), array_keys($key_value_pairs));

        if($schema_difference){

            $schema_difference_label = implode(", ", $schema_difference);

            throw new Exception("The following key(s) from the schema are missing in this key-value pair: $schema_difference_label");

        }
        
        foreach($key_value_pairs as $key=>$value){

            if(!isset($schema_rules[$key])){

                throw new Exception("Existing schema does not include the key named '$key'");
                
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
                
                throw new Exception("$key must be $expected_type");

            }

        }

    }


    public static function validateSchema(array $key_value_pairs) {
        
        foreach ($key_value_pairs as $key_value) {
            
            if(!in_array($key_value['value'], static::$allowed_types)){

                $allowed_types_label = implode(", ", static::$allowed_types);

                throw new Exception("The value must be one of $allowed_types_label");

            }
        }

    }
}