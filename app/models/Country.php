<?php

use Illuminate\Database\Eloquent\Model;

class Country extends Model {

    protected $table = 'country';
    protected $primaryKey = 'idcountry';
    public $timestamps = false;

}

?>