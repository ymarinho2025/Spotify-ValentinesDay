<?php
require_once __DIR__.'/config/auth.php';
$pdo = db();
$pdo->exec(file_get_contents(__DIR__.'/sql/schema.sql'));
$user = require_user();
require_once __DIR__.'/config/seed.php';
$msg=''; $err=''; $inviteLink='';

function h($x){ return htmlspecialchars((string)$x, ENT_QUOTES, 'UTF-8'); }
function validate_image(array $f): array {
    if (($f['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_OK) throw new Exception('Selecione uma imagem.');
    if (($f['size']??0) > 4*1024*1024) throw new Exception('A imagem deve ter no máximo 4 MB.');
    $tmp=$f['tmp_name']; $fi=new finfo(FILEINFO_MIME_TYPE); $mime=$fi->file($tmp)?:'';
    $allowed=['image/jpeg','image/png','image/webp','image/gif'];
    if(!in_array($mime,$allowed,true)) throw new Exception('Formato inválido. Use JPG, PNG, WEBP ou GIF.');
    $raw=file_get_contents($tmp); if($raw===false) throw new Exception('Não foi possível ler a imagem.');
    return [$mime,$raw];
}
function save_db_image(PDO $pdo, array $file, string $name, int $uid): int {
    [$mime,$raw]=validate_image($file);
    $s=$pdo->prepare("INSERT INTO couple_images(display_name,original_name,mime_type,base64_data,size_bytes,source_type,created_by,updated_by) VALUES(:n,:o,:m,:b,:z,'database',:u,:u) RETURNING id");
    $s->execute([':n'=>$name,':o'=>basename($file['name']),':m'=>$mime,':b'=>base64_encode($raw),':z'=>strlen($raw),':u'=>$uid]);
    return (int)$s->fetchColumn();
}

try { seed_couple($pdo,(int)$user['id']); }
catch(Throwable $e) { if($pdo->inTransaction())$pdo->rollBack(); $err='Não foi possível concluir a sincronização do conteúdo original: '.$e->getMessage(); }

if($_SERVER['REQUEST_METHOD']==='POST'){
    try{
        verify_csrf(); $kind=$_POST['kind']??'';
        if($kind==='settings'){
            $names=trim($_POST['names']??''); $start=$_POST['start_date']??''; $met=$_POST['first_met']??'';
            $about=trim($_POST['about_text']??''); $final=trim($_POST['final_message']??'');
            if($names==='') throw new Exception('Informe os nomes.');
            $pdo->prepare('UPDATE couple_settings SET names=:n,start_date=:s,first_met=:m,about_text=:a,final_message=:f,updated_by=:u,updated_at=NOW() WHERE id=1')
                ->execute([':n'=>$names,':s'=>$start,':m'=>$met,':a'=>$about,':f'=>$final,':u'=>$user['id']]);
            audit($pdo,$user['id'],'update','settings',1,['names'=>$names]); $msg='Informações do casal atualizadas.';
        }
        elseif($kind==='avatar'){
            $slot=$_POST['slot']??''; if(!in_array($slot,['her','me'],true)) throw new Exception('Avatar inválido.');
            $img=save_db_image($pdo,$_FILES['image']??[],$slot==='her'?'Foto dela':'Foto dele',(int)$user['id']);
            $column=$slot==='her'?'avatar_her_id':'avatar_me_id';
            $pdo->prepare("UPDATE couple_settings SET {$column}=:i,updated_by=:u,updated_at=NOW() WHERE id=1")->execute([':i'=>$img,':u'=>$user['id']]);
            audit($pdo,$user['id'],'update','avatar',1,['slot'=>$slot]); $msg='Foto do casal atualizada.';
        }
        elseif($kind==='chapter'){
            $id=(int)($_POST['id']??0); $label=trim($_POST['chapter_label']??''); $title=trim($_POST['title']??'');
            $desc=trim($_POST['description']??''); $order=(int)($_POST['sort_order']??0); $active=isset($_POST['active']);
            if(!$id||$title==='') throw new Exception('Capítulo inválido.');
            $pdo->prepare('UPDATE couple_chapters SET chapter_label=:l,title=:t,description=:d,sort_order=:o,active=:a,updated_by=:u,updated_at=NOW() WHERE id=:id')
                ->execute([':l'=>$label,':t'=>$title,':d'=>$desc,':o'=>$order,':a'=>$active,':u'=>$user['id'],':id'=>$id]);
            if(isset($_FILES['image'])&&($_FILES['image']['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_NO_FILE){
                $img=save_db_image($pdo,$_FILES['image'],$title,(int)$user['id']);
                $pdo->prepare('UPDATE couple_chapters SET image_id=:i WHERE id=:id')->execute([':i'=>$img,':id'=>$id]);
            }
            audit($pdo,$user['id'],'update','chapter',$id,['title'=>$title]); $msg='Capítulo atualizado.';
        }
        elseif($kind==='new_chapter'){
            $max=(int)$pdo->query('SELECT COALESCE(MAX(sort_order),0)+1 FROM couple_chapters')->fetchColumn();
            $s=$pdo->prepare('INSERT INTO couple_chapters(sort_order,chapter_label,title,description,active,created_by,updated_by) VALUES(:o,:l,:t,:d,TRUE,:u,:u) RETURNING id');
            $s->execute([':o'=>$max,':l'=>'Capítulo '.$max,':t'=>'Novo momento',':d'=>'',':u'=>$user['id']]);
            audit($pdo,$user['id'],'create','chapter',(int)$s->fetchColumn()); $msg='Novo capítulo criado.';
        }
        elseif($kind==='delete_chapter'){
            $id=(int)($_POST['id']??0); $pdo->prepare('DELETE FROM couple_chapters WHERE id=:id')->execute([':id'=>$id]);
            audit($pdo,$user['id'],'delete','chapter',$id); $msg='Capítulo excluído.';
        }
        elseif($kind==='create_invite'){
            $count=(int)$pdo->query('SELECT COUNT(*) FROM couple_users')->fetchColumn();
            if($count>=2) throw new Exception('As duas contas do casal já foram criadas.');
            $pdo->prepare("UPDATE couple_invites SET status='revoked' WHERE status='pending'")->execute();
            $raw=bin2hex(random_bytes(24)); $hash=hash('sha256',$raw);
            $pdo->prepare("INSERT INTO couple_invites(token_hash,invited_by,expires_at) VALUES(:h,:u,NOW()+INTERVAL '7 days')")
                ->execute([':h'=>$hash,':u'=>$user['id']]);
            $scheme=((!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')||($_SERVER['HTTP_X_FORWARDED_PROTO']??'')==='https')?'https':'http';
            $host=$_SERVER['HTTP_HOST']??''; $inviteLink=$scheme.'://'.$host.'/convite-casal.php?token='.$raw;
            $msg='Convite criado. Envie o link abaixo somente para seu par; ele expira em 7 dias.';
        }
        elseif($kind==='repair_original'){
            seed_couple($pdo,(int)$user['id'],true); $msg='Conteúdo original verificado e restaurado quando estava faltando.';
        }
    }catch(Throwable $e){ if($pdo->inTransaction())$pdo->rollBack(); $err=$e->getMessage(); }
}

$defaults=['id'=>1,'names'=>'Sarah & Yuri','start_date'=>'2026-07-16','first_met'=>'2026-06-13','about_text'=>'','final_message'=>'Nossa história só está começando ✨','avatar_her_id'=>null,'avatar_me_id'=>null];
$row=$pdo->query('SELECT id,names,start_date,first_met,about_text,final_message,avatar_her_id,avatar_me_id,seed_version,updated_by,updated_at FROM couple_settings WHERE id=1')->fetch(PDO::FETCH_ASSOC); $settings=array_merge($defaults,$row?:[]);
$chapters=$pdo->query('SELECT c.*,i.display_name image_name FROM couple_chapters c LEFT JOIN couple_images i ON i.id=c.image_id ORDER BY c.sort_order,c.id')->fetchAll(PDO::FETCH_ASSOC);
$users=$pdo->query('SELECT id,name,email,created_at FROM couple_users ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
$userCount=count($users);
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Painel do Casal</title><style>
:root{--green:#1ed760;--bg:#0b0b0b;--card:#171717;--card2:#222;--muted:#aaa;--line:#333}*{box-sizing:border-box}body{margin:0;background:var(--bg);color:#fff;font-family:Montserrat,Arial,sans-serif}.top{position:sticky;top:0;z-index:5;background:#0b0b0bee;border-bottom:1px solid var(--line);padding:16px clamp(16px,4vw,50px);display:flex;justify-content:space-between;align-items:center;gap:16px}.brand{font-weight:900;font-size:21px}.brand span{color:var(--green)}.top a{color:#fff;text-decoration:none;margin-left:14px;font-size:13px}.wrap{max-width:1100px;margin:auto;padding:30px 18px 70px}.hero{display:flex;justify-content:space-between;gap:20px;align-items:end;margin-bottom:24px}.hero h1{margin:0;font-size:clamp(32px,5vw,54px)}.hero p{color:var(--muted)}.notice{padding:12px 14px;border-radius:10px;margin:12px 0}.ok{background:#14391f}.err{background:#4c1818}.panel{background:var(--card);border:1px solid var(--line);border-radius:18px;padding:22px;margin:20px 0}.panel h2{margin-top:0}.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.field label,.chapter label{font-size:12px;font-weight:700;color:#ddd;display:block;margin-bottom:6px}.field input,.field textarea,.chapter input,.chapter textarea{width:100%;background:#242424;border:1px solid #444;border-radius:8px;color:#fff;padding:12px;font-size:15px}.field textarea,.chapter textarea{min-height:120px;resize:vertical}.btn{border:0;border-radius:999px;padding:12px 18px;background:var(--green);color:#000;font-weight:800;cursor:pointer}.btn.secondary{background:#333;color:#fff}.btn.danger{background:#652020;color:#fff}.chapters{display:grid;gap:18px}.chapter{display:grid;grid-template-columns:180px 1fr;gap:20px;padding:18px;background:var(--card2);border-radius:14px}.thumb{width:180px;height:180px;border-radius:12px;object-fit:cover;background:#111}.row{display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-top:12px}.mini{display:grid;grid-template-columns:120px 1fr;gap:10px}.file{border:1px dashed #555;padding:10px;border-radius:8px}.hint{font-size:12px;color:var(--muted)}.avatar-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.avatar-card{background:#222;padding:16px;border-radius:14px}.avatar-card img{width:120px;height:120px;object-fit:cover;border-radius:50%;display:block;margin-bottom:12px}.member{padding:12px;background:#222;border-radius:10px;margin:8px 0}.invite{width:100%;background:#0d0d0d;border:1px solid #444;color:#fff;padding:12px;border-radius:8px}.count{display:inline-block;background:#242424;padding:6px 10px;border-radius:999px;color:#ddd;font-size:12px}@media(max-width:700px){.grid,.chapter,.mini,.avatar-grid{grid-template-columns:1fr}.thumb{width:100%;height:260px}.top{align-items:flex-start}.top nav{display:grid;gap:7px}.top a{margin:0}}
</style></head><body>
<header class="top"><div class="brand">Couple <span>CMS</span> ♥</div><nav><a href="/" target="_blank">Ver site</a><a href="/login-casal.php?logout=1">Sair</a></nav></header>
<main class="wrap"><section class="hero"><div><h1>Painel do Casal</h1><p>Edite fotos, textos e capítulos sem alterar o código.</p></div><div class="hint">Conectado como <?=h($user['name'])?></div></section>
<?php if($msg):?><div class="notice ok"><?=h($msg)?></div><?php endif;?><?php if($err):?><div class="notice err"><?=h($err)?></div><?php endif;?>
<?php if($inviteLink):?><section class="panel"><h2>Convite do seu par</h2><p class="hint">Este link só pode criar a segunda conta. Depois disso, novos cadastros ficam bloqueados.</p><input class="invite" readonly value="<?=h($inviteLink)?>" onclick="this.select()"></section><?php endif;?>

<section class="panel"><div class="hero"><div><h2>Contas do casal <span class="count"><?=$userCount?>/2</span></h2><p>Cada pessoa tem seu próprio e-mail e senha, mas as duas editam o mesmo site.</p></div><?php if($userCount<2):?><form method="post"><input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><input type="hidden" name="kind" value="create_invite"><button class="btn">Convidar parceiro(a)</button></form><?php endif;?></div>
<?php foreach($users as $u):?><div class="member"><strong><?=h($u['name'])?></strong><div class="hint"><?=h($u['email'])?><?=$u['id']==$user['id']?' · você':''?></div></div><?php endforeach;?>
<?php if($userCount>=2):?><div class="hint" style="margin-top:12px">As duas vagas do casal estão ocupadas. O banco bloqueia a criação de uma terceira conta.</div><?php endif;?></section>

<section class="panel"><h2>Informações gerais</h2><form method="post"><input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><input type="hidden" name="kind" value="settings"><div class="grid"><div class="field"><label>Nomes</label><input name="names" value="<?=h($settings['names'])?>"></div><div class="field"><label>Mensagem final</label><input name="final_message" value="<?=h($settings['final_message'])?>"></div><div class="field"><label>Início do namoro</label><input type="date" name="start_date" value="<?=h($settings['start_date'])?>"></div><div class="field"><label>Quando se conheceram</label><input type="date" name="first_met" value="<?=h($settings['first_met'])?>"></div></div><div class="field" style="margin-top:14px"><label>Texto “Sobre nós”</label><textarea name="about_text"><?=h($settings['about_text'])?></textarea></div><div class="row"><button class="btn">Salvar informações</button></div></form></section>

<section class="panel"><h2>Fotos do casal</h2><p class="hint">As fotos que já existiam no site ficam disponíveis aqui e podem ser substituídas pelo painel.</p><div class="avatar-grid">
<div class="avatar-card"><img src="/api/couple-image.php?id=<?=(int)$settings['avatar_her_id']?>" alt="Foto dela"><strong>Foto dela</strong><form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><input type="hidden" name="kind" value="avatar"><input type="hidden" name="slot" value="her"><input class="file" type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" required><div class="row"><button class="btn">Trocar foto</button></div></form></div>
<div class="avatar-card"><img src="/api/couple-image.php?id=<?=(int)$settings['avatar_me_id']?>" alt="Foto dele"><strong>Foto dele</strong><form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><input type="hidden" name="kind" value="avatar"><input type="hidden" name="slot" value="me"><input class="file" type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" required><div class="row"><button class="btn">Trocar foto</button></div></form></div>
</div></section>

<section class="panel"><div class="hero"><div><h2>Retrospectiva</h2><p>Todos os capítulos e fotos do projeto original aparecem abaixo.</p></div><div class="row"><form method="post"><input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><input type="hidden" name="kind" value="repair_original"><button class="btn secondary">Restaurar itens originais faltantes</button></form><form method="post"><input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><input type="hidden" name="kind" value="new_chapter"><button class="btn">+ Novo capítulo</button></form></div></div>
<div class="chapters"><?php foreach($chapters as $c):?><div><form class="chapter" method="post" enctype="multipart/form-data"><div><img class="thumb" src="/api/couple-image.php?id=<?=(int)$c['image_id']?>" alt=""><div class="hint" style="margin-top:8px"><?=h($c['image_name']??'Sem foto')?><?=!empty($c['seed_key'])?' · original':''?></div></div><div><input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><input type="hidden" name="kind" value="chapter"><input type="hidden" name="id" value="<?=(int)$c['id']?>"><div class="mini"><div><label>Ordem</label><input type="number" name="sort_order" value="<?=(int)$c['sort_order']?>"></div><div><label>Rótulo</label><input name="chapter_label" value="<?=h($c['chapter_label'])?>"></div></div><label style="margin-top:10px">Título</label><input name="title" value="<?=h($c['title'])?>" required><label style="margin-top:10px">Descrição</label><textarea name="description"><?=h($c['description'])?></textarea><label style="margin-top:10px">Trocar foto</label><input class="file" type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif"><div class="hint">Até 4 MB. Fotos novas são armazenadas em Base64 no NeonDB.</div><div class="row"><label><input type="checkbox" name="active" <?=$c['active']?'checked':''?>> Exibir no Rewind</label><button class="btn">Salvar capítulo</button></div></div></form><form method="post" onsubmit="return confirm('Excluir este capítulo?')" style="margin-top:8px;text-align:right"><input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><input type="hidden" name="kind" value="delete_chapter"><input type="hidden" name="id" value="<?=(int)$c['id']?>"><button class="btn danger">Excluir</button></form></div><?php endforeach;?></div>
</section></main></body></html>
