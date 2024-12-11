<?php

use Illuminate\Database\Eloquent\Model;

class LabUser extends Model {

    protected $table = 'labUser';
    protected $primaryKey = 'luid';
    public $timestamps = false;
    
     public function lab() {
        return $this->belongsToMany('Lab','labuser_luid');
    }
    
    //pasi
    public function user() {
        return $this->belongsToMany('user','user_luid');
    }
    
}

?>