<?php
require_once __DIR__.'/config/auth.php';
$pdo=db();
$pdo->exec(file_get_contents(__DIR__.'/sql/schema.sql'));
$token=(string)($_GET['token']??$_POST['token']??'');
$err=''; $valid=null;
function h($x){return htmlspecialchars((string)$x,ENT_QUOTES,'UTF-8');}
if($token!==''){
    $s=$pdo->prepare("SELECT i.*,u.name inviter_name FROM couple_invites i JOIN couple_users u ON u.id=i.invited_by WHERE i.token_hash=:h AND i.status='pending' AND i.expires_at>NOW() LIMIT 1");
    $s->execute([':h'=>hash('sha256',$token)]); $valid=$s->fetch(PDO::FETCH_ASSOC)?:null;
}
if(!$valid) $err='Este convite é inválido, expirou ou já foi utilizado.';
if($_SERVER['REQUEST_METHOD']==='POST' && $valid){
    try{
        verify_csrf();
        if((int)$pdo->query('SELECT COUNT(*) FROM couple_users')->fetchColumn()>=2) throw new Exception('As duas contas do casal já foram criadas.');
        $name=trim($_POST['name']??''); $email=trim($_POST['email']??''); $pass=$_POST['password']??''; $confirm=$_POST['confirm']??'';
        if(strlen($name)<2) throw new Exception('Informe seu nome.');
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)) throw new Exception('Informe um e-mail válido.');
        if(strlen($pass)<8) throw new Exception('A senha deve ter pelo menos 8 caracteres.');
        if($pass!==$confirm) throw new Exception('As senhas não coincidem.');
        $pdo->beginTransaction();
        $ins=$pdo->prepare("INSERT INTO couple_users(name,email,password) VALUES(:n,:e,:p) RETURNING id,name,email,status");
        $ins->execute([':n'=>$name,':e'=>$email,':p'=>password_hash($pass,PASSWORD_DEFAULT)]); $u=$ins->fetch(PDO::FETCH_ASSOC);
        $pdo->prepare("UPDATE couple_invites SET status='accepted',accepted_by=:u,accepted_at=NOW() WHERE id=:id AND status='pending'")->execute([':u'=>$u['id'],':id'=>$valid['id']]);
        $pdo->prepare("UPDATE couple_invites SET status='revoked' WHERE status='pending' AND id<>:id")->execute([':id'=>$valid['id']]);
        $pdo->commit(); issue_cookie($u); header('Location:/casal.php'); exit;
    }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();$err=$e->getMessage();}
}
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Convite do Casal</title><style>body{margin:0;background:#0b0b0b;color:#fff;font-family:Arial,sans-serif}.box{min-height:100vh;display:grid;place-items:center;padding:20px}.card{width:min(450px,100%);padding:30px;background:#181818;border-radius:20px}.card h1{font-size:28px}.card input{display:block;width:100%;box-sizing:border-box;margin:7px 0 16px;padding:13px;background:#242424;border:1px solid #555;border-radius:8px;color:#fff;font-size:16px}.card button{width:100%;padding:14px;border:0;border-radius:999px;background:#1ed760;font-weight:800}.err{background:#4a1919;padding:10px;border-radius:8px}.hint{color:#aaa}</style></head><body><main class="box"><form class="card" method="post"><h1>Entrar no nosso casal ♥</h1><?php if($valid):?><p class="hint"><?=h($valid['inviter_name'])?> convidou você para ter a segunda conta do Couple CMS.</p><?php endif;?><?php if($err):?><div class="err"><?=h($err)?></div><?php endif;?><?php if($valid):?><input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><input type="hidden" name="token" value="<?=h($token)?>"><label>Seu nome</label><input name="name" required><label>Seu e-mail</label><input type="email" name="email" required><label>Sua senha</label><input type="password" name="password" minlength="8" required><label>Confirmar senha</label><input type="password" name="confirm" minlength="8" required><button>Criar minha conta</button><?php endif;?></form></main></body></html>
