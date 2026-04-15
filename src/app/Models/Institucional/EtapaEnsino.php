<?php

namespace App\Models\Institucional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EtapaEnsino extends Model
{
    use HasFactory;

    protected $table = 'etapas_ensino';

    protected $fillable = ['municipio_id', 'nome', 'ordem', 'ativo'];

    protected $casts = ['ativo' => 'boolean'];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function series()
    {
        return $this->hasMany(Serie::class)->orderBy('ordem');
    }
}
