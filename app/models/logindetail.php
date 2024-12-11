<?php

use Illuminate\Database\Eloquent\Model;

class logindetail extends Model{
    protected $table = 'loginDetails';
    protected $primaryKey = 'idloginDetails';
    
    public $timestamps = false;
}

?>