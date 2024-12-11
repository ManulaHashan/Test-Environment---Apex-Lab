<?php

use Illuminate\Database\Eloquent\Model;

class User extends Model {

    protected $table = 'user';
    protected $primaryKey = 'uid';
    public $timestamps = false;
  
    public function labuser() {
        return $this->hasOne('Labuser','user_uid');
    }
}

?>