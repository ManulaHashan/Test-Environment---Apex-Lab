<?php

use Illuminate\Database\Eloquent\Model;

class Gender extends Model {

    protected $table = 'gender';
    protected $primaryKey = 'idgender';
    
    public $timestamps = false;

}

?>