<?php
include($_SERVER['DOCUMENT_ROOT'].'/ajax/checkAjax.php');
session_start();
require $_SERVER['DOCUMENT_ROOT'].'/classes/classMain.php';
include $_SERVER['DOCUMENT_ROOT'].'/included/variables.php';
$main = new main;
$user = $_POST['user'];
$password = md5($_POST['password']);
$cryptPass = $main->encrypt($password, $secretKey);
$cryptUser = $main->encrypt($user, $secretKey);
$login=$main->selectRow('customer',"LOWER(CusName)=LOWER('$user') AND CusPass='$password' AND adminLevel>0 AND Active = 1");

$field = 'user,time,action,ip';
$data = NULL;
$time = time();
$ip = $_SERVER['REMOTE_ADDR'];
if(!!$login){
    $data =	"'$user',$time,'<span class=\"green\">Đăng nhập thành công</span>','$ip'";
    $_SESSION['admin_id']=$login['CusID'];
    if(isset($_POST['remember'])){
        setcookie('adata1',$cryptUser,time()+60*60*24*30*12,'/');
        setcookie('adata2',$cryptPass,time()+60*60*24*30*12,'/');
    }
    echo '1';
}else{
    $data =	"'$user',$time,'<span class=\"dark-red\">Đăng nhập thất bại</span>','$ip'";
    echo '0';
}
$main->insert('admin_logs',$field,$data);
//print_r( $_POST);
?>