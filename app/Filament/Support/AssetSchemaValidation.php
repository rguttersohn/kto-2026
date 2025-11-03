<?php

namespace App\Filament\Support;

use App\Models\AssetSchema;
use Exception;


class AssetSchemaValidation {
    
    protected static array $allowed_types = ['numeric', 'string'];
    
    public static function validateData(AssetSchema $asset_schema, array $key_value_pairs): bool | Exception{
        
        if(!$asset_schema){

            return true;
        }

        $schema_rules = $asset_schema->schema;
        
        foreach($key_value_pairs as $key_value){
                                
            $key = $key_value['key'] ?? null;
            $item_value = $key_value['value'] ?? null;

            $expected_type = $schema_rules[$key] ?? null;

            if(!$expected_type){

                continue;
            }

            $validator = validator(
                ['value' => $item_value],
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