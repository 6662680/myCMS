<?php if(!defined('IN_UCMS')) die('Access denied.');
use  Illuminate\Database\Eloquent\Model  as Eloquent;
class user extends Eloquent {
  protected $table = 'user';

  public function indexAction(){

    $users = User::find(2); //查找ID为2的
    print_r($users);
  }
}
