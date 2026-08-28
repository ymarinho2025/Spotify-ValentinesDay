<?php
require_once __DIR__.'/database.php';
function jwt_key():string{$k=getenv('JWT_SECRET');if(!$k||strlen($k)<32)throw new RuntimeException('JWT_SECRET ausente ou curto (mínimo 32 caracteres).');return $k;}
function b64u_enc(string $s):string{return rtrim(strtr(base64_encode($s),'+/','-_'),'=');}
function b64u_dec(string $s):string|false{$p=strlen($s)%4;if($p)$s.=str_repeat('=',4-$p);return base64_decode(strtr($s,'-_','+/'),true);}
function token_sign(array $payload):string{$h=b64u_enc(json_encode(['alg'=>'HS256','typ'=>'JWT']));$p=b64u_enc(json_encode($payload,JSON_UNESCAPED_UNICODE));$sig=hash_hmac('sha256',$h.'.'.$p,jwt_key(),true);return $h.'.'.$p.'.'.b64u_enc($sig);}
function token_decode(string $token):?array{$parts=explode('.',$token);if(count($parts)!==3)return null;[$h,$p,$s]=$parts;$sig=b64u_dec($s);if($sig===false||!hash_equals(hash_hmac('sha256',$h.'.'.$p,jwt_key(),true),$sig))return null;$raw=b64u_dec($p);if($raw===false)return null;$d=json_decode($raw,true);if(!is_array($d)||($d['exp']??0)<time())return null;return $d;}
function cookie_opts(int $exp):array{$https=(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')||(($_SERVER['HTTP_X_FORWARDED_PROTO']??'')==='https');return ['expires'=>$exp,'path'=>'/','httponly'=>true,'secure'=>$https,'samesite'=>'Lax'];}
function issue_cookie(array $u):void{$now=time();$t=token_sign(['iat'=>$now,'exp'=>$now+86400,'id'=>(int)$u['id'],'name'=>$u['name']??'']);setcookie('couple_auth',$t,cookie_opts($now+86400));$_COOKIE['couple_auth']=$t;}
function clear_cookie():void{setcookie('couple_auth','',cookie_opts(time()-3600));unset($_COOKIE['couple_auth']);}
function current_user(?PDO $pdo=null):?array{$t=$_COOKIE['couple_auth']??'';if($t==='')return null;try{$d=token_decode($t);if(!$d)throw new Exception('inválido');$pdo=$pdo?:db();$s=$pdo->prepare("SELECT id,name,email,status FROM couple_users WHERE id=:id LIMIT 1");$s->execute([':id'=>(int)$d['id']]);$u=$s->fetch();if(!$u||$u['status']!=='active'){clear_cookie();return null;}return $u;}catch(Throwable $e){clear_cookie();return null;}}
function require_user():array{$u=current_user();if(!$u){header('Location:/login-casal.php');exit;}return $u;}
function verify_password(PDO $pdo,array $u,string $pass):bool{$stored=(string)$u['password'];if($stored&&password_verify($pass,$stored)){if(password_needs_rehash($stored,PASSWORD_DEFAULT))$pdo->prepare('UPDATE couple_users SET password=:p WHERE id=:id')->execute([':p'=>password_hash($pass,PASSWORD_DEFAULT),':id'=>$u['id']]);return true;}return false;}
function csrf_context():string{
    $auth=$_COOKIE['couple_auth']??'';
    if($auth!=='')return 'auth|'.hash('sha256',$auth);
    $host=strtolower((string)($_SERVER['HTTP_HOST']??'localhost'));
    return 'guest|'.$host;
}
function csrf_token():string{
    return hash_hmac('sha256','couple-csrf|'.csrf_context(),jwt_key());
}
function request_is_same_origin():bool{
    $host=strtolower(preg_replace('/:\d+$/','',(string)($_SERVER['HTTP_HOST']??'')));
    if($host==='')return true;
    $origin=(string)($_SERVER['HTTP_ORIGIN']??'');
    if($origin!==''){
        $oh=strtolower((string)(parse_url($origin,PHP_URL_HOST)??''));
        return $oh!==''&&hash_equals($host,$oh);
    }
    $referer=(string)($_SERVER['HTTP_REFERER']??'');
    if($referer!==''){
        $rh=strtolower((string)(parse_url($referer,PHP_URL_HOST)??''));
        return $rh!==''&&hash_equals($host,$rh);
    }
    return true;
}
function verify_csrf():void{
    $submitted=(string)($_POST['csrf']??($_SERVER['HTTP_X_CSRF_TOKEN']??''));
    $expected=csrf_token();
    if($submitted===''||!hash_equals($expected,$submitted)||!request_is_same_origin()){
        http_response_code(419);
        exit('Token CSRF inválido. Atualize a página e tente novamente.');
    }
}
function audit(PDO $pdo,int $uid,string $action,string $entity,?int $eid,array $changes=[]):void{try{$pdo->prepare('INSERT INTO couple_audit(user_id,action,entity_type,entity_id,changes_json,ip,user_agent) VALUES(:u,:a,:e,:id,:c,:ip,:ua)')->execute([':u'=>$uid,':a'=>$action,':e'=>$entity,':id'=>$eid,':c'=>json_encode($changes,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),':ip'=>substr($_SERVER['REMOTE_ADDR']??'',0,45),':ua'=>substr($_SERVER['HTTP_USER_AGENT']??'',0,500)]);}catch(Throwable $e){}}
