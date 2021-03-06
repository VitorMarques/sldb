<?php

namespace sldb\Models;

use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{

    protected $table = 'tb_perfil';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'descricao',
    ];

    public $guarded = ['id'];

    public $timestamps = false;

}
