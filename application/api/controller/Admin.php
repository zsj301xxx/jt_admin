<?php


namespace app\api\controller;


use app\model\AdminModel;
use think\captcha\Captcha;

class Admin
{
//    function createHash(){
//        return md5(time());
//    }
    function createPassword($pass,$hash){
        return md5(sha1($pass).$hash);
    }

  function captcha(){
      $config=[
          // 验证码字体大小
          'fontSize'    =>    30,
          // 验证码位数
          'length'      =>    4,
          // 关闭验证码杂点
          'useNoise'    =>    false,
      ];
      $captcha = new Captcha($config);
      return $captcha->entry();
  }
  function login(){
     $username=input("post.username");
     $password=input("post.password");
     $code=input("post.captcha");
     $captcha=new Captcha();
      if (!$captcha->check($code)){
          return json(["msg"=>"验证码错误","code"=>400]);
      }
      $r=AdminModel::where("username",$username)->find();
      if (isset($r)){
          $pass=$this->createPassword($password,$r->hash);
          if ($pass===$r->password){
              return json(["msg"=>"登录成功","code"=>200]);
          }
          return json(["msg"=>"登录失败","code"=>400]);
      }else{
          return json(["msg"=>"登录失败","code"=>400]);
      }
  }
  public function password(){
       $data=input("put.");
       $r=AdminModel::where("username",$data['username'])->find();
       if (isset($r)){

         $pass=$this->createPassword($data['password'],$r->hash);
         if ($pass===$r->password){
             $new=$this->createPassword($data["password1"],$r->hash);
            $res=$r->save(["password"=>$new]);
            if ($res){
                return json(["msg"=>"修改成功","code"=>200]);
            }else{
                return json(["msg"=>"修改错误","code"=>400]);
            }
         }else{
             return json(["msg"=>"原始错误","code"=>400]);
         }
       }else{
           return json(["msg"=>"用户名错误","code"=>400]);
       }


  }


}