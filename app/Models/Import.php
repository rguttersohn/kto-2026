<?php

namespace App\Models;

use Filament\Actions\Imports\Models\Import as FilamentImport;

class Import extends FilamentImport
{
    protected $table = 'app.imports';

    public function failedImports(){

        return $this->hasMany(FailedImport::class, 'import_id', 'id');
    }
}
