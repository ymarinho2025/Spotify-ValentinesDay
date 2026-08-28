<?php
/**
 * Garante que TODO o conteúdo original do projeto esteja disponível no CMS.
 * É idempotente e pode ser executado a cada login sem duplicar os itens.
 */
function seed_couple(PDO $pdo, int $uid, bool $force=false): void
{
    // Depois da migração completa, não recria itens que o casal apagar de propósito.
    try {
        $version=(int)$pdo->query("SELECT COALESCE(seed_version,0) FROM couple_settings WHERE id=1")->fetchColumn();
        if(!$force && $version>=2) return;
    } catch (Throwable $e) {}
    $about = 'Nossa história começou na Tribo, em 13/06/2026. Desde então, cada dia trouxe uma nova lembrança, um novo sorriso e mais um motivo para acreditar que as melhores histórias são escritas pouco a pouco. Eu poderia encher um livro inteiro dizendo o quanto você é importante para mim, mas talvez ele ficasse maior que um Vade Mecum. Então escolhi uma forma diferente de registrar tudo isso: este site. Aqui estão reunidos os momentos que marcaram nossa caminhada, transformados em páginas de uma retrospectiva feita com carinho, amor e gratidão. Espero que, ao percorrê-la, você sinta um pouco de tudo o que sinto por você e perceba que a nossa história é, sem dúvida, a mais bonita que eu poderia viver.';

    // A linha de configurações precisa existir sempre. Não sobrescreve personalizações existentes.
    $settings = $pdo->prepare(
        "INSERT INTO couple_settings(id,names,start_date,first_met,about_text,final_message,updated_by)
         VALUES(1,'Sarah & Yuri','2026-07-16','2026-06-13',:about,'Nossa história só está começando ✨',:uid)
         ON CONFLICT(id) DO NOTHING"
    );
    $settings->execute([':about' => $about, ':uid' => $uid]);

    $imageSeeds = [
        ['avatar-her', 'Sarah', 'her.jpg', 'images/her.jpg'],
        ['avatar-me',  'Yuri',  'me.jpg',  'images/me.jpg'],
        ['chapter-tribo', 'Tribo', 'photo.jpg', 'images/photo.jpg'],
        ['chapter-gincana', 'Gincana', 'photo1.jpg', 'images/photo1.jpg'],
        ['chapter-primeiro-encontro', 'Primeiro encontro', 'photo2.jpg', 'images/photo2.jpg'],
        ['chapter-passeando', 'Passeando com a cara metade', 'photo3.jpg', 'images/photo3.jpg'],
        ['chapter-final-semana', 'Final de semana juntos', 'photo4.jpg', 'images/photo4.jpg'],
        ['chapter-cinema', 'Cinema', 'photo5.jpg', 'images/photo5.jpg'],
        ['chapter-te-amo', 'TE AMO SARAH', 'photo6.jpg', 'images/photo6.jpg'],
    ];

    $findSeedImage = $pdo->prepare("SELECT id FROM couple_images WHERE seed_key=:seed LIMIT 1");
    $findLegacyImage = $pdo->prepare("SELECT id FROM couple_images WHERE source_type='local' AND local_path=:path ORDER BY id LIMIT 1");
    $insertImage = $pdo->prepare(
        "INSERT INTO couple_images(display_name,original_name,mime_type,size_bytes,source_type,local_path,seed_key,created_by,updated_by)
         VALUES(:name,:original,'image/jpeg',0,'local',:path,:seed,:uid,:uid) RETURNING id"
    );
    $claimLegacyImage = $pdo->prepare("UPDATE couple_images SET seed_key=:seed,updated_by=:uid,updated_at=NOW() WHERE id=:id AND seed_key IS NULL");

    $imageIds = [];
    foreach ($imageSeeds as [$seed, $name, $original, $path]) {
        $findSeedImage->execute([':seed' => $seed]);
        $id = $findSeedImage->fetchColumn();
        if (!$id) {
            $findLegacyImage->execute([':path' => $path]);
            $legacy = $findLegacyImage->fetchColumn();
            if ($legacy) {
                $id = (int)$legacy;
                try { $claimLegacyImage->execute([':seed'=>$seed, ':uid'=>$uid, ':id'=>$id]); } catch (Throwable $e) {}
            } else {
                $insertImage->execute([
                    ':name'=>$name, ':original'=>$original, ':path'=>$path, ':seed'=>$seed, ':uid'=>$uid
                ]);
                $id = (int)$insertImage->fetchColumn();
            }
        }
        $imageIds[$seed] = (int)$id;
    }

    // Avatares originais também ficam administráveis no painel.
    $pdo->prepare(
        "UPDATE couple_settings
         SET avatar_her_id=COALESCE(avatar_her_id,:her), avatar_me_id=COALESCE(avatar_me_id,:me)
         WHERE id=1"
    )->execute([':her'=>$imageIds['avatar-her'], ':me'=>$imageIds['avatar-me']]);

    $chapters = [
        ['chapter-tribo', 0, 'O início de tudo', 'Tribo', 'Em meio a tantas pessoas, Deus permitiu que nossos caminhos se encontrassem e que uma linda história começasse a ser escrita.'],
        ['chapter-gincana', 1, 'Capítulo 1', 'Gincana', 'Foi na gincana que pudemos nos aproximar ainda mais. Eu não estava tão animado para participar, até perceber que seria uma oportunidade de nos vermos e conversarmos.'],
        ['chapter-primeiro-encontro', 2, 'Capítulo 2', 'Primeiro encontro', 'Passamos nosso primeiro final de semana inteiro juntos. Entre conversas, risadas e momentos simples, criamos lembranças que levaremos para sempre em nossos corações.'],
        ['chapter-passeando', 3, 'Capítulo 3', 'Passeando com a cara metade', 'Descrições por Amore Mio/Sarah 😜'],
        ['chapter-final-semana', 4, 'Capítulo 4', 'Final de semana juntos', 'Descrições por Amore Mio/Sarah 😜'],
        ['chapter-cinema', 5, 'Capítulo 5', 'Cinema', 'Ao lado da minha Mary Jane. Mas, diferente do Peter Parker, essa história não vai acabar. 😜❤️'],
        ['chapter-te-amo', 6, 'Capítulo 6', 'TE AMO SARAH', 'Descrições por Amore Mio/Sarah 😜'],
    ];

    $findSeedChapter = $pdo->prepare("SELECT id,image_id FROM couple_chapters WHERE seed_key=:seed LIMIT 1");
    $findLegacyChapter = $pdo->prepare("SELECT id,image_id FROM couple_chapters WHERE title=:title AND seed_key IS NULL ORDER BY id LIMIT 1");
    $insertChapter = $pdo->prepare(
        "INSERT INTO couple_chapters(sort_order,chapter_label,title,description,image_id,seed_key,active,created_by,updated_by)
         VALUES(:ord,:label,:title,:description,:image,:seed,TRUE,:uid,:uid) RETURNING id"
    );
    $claimLegacyChapter = $pdo->prepare(
        "UPDATE couple_chapters SET seed_key=:seed,image_id=COALESCE(image_id,:image),updated_by=:uid,updated_at=NOW() WHERE id=:id"
    );
    $attachMissingImage = $pdo->prepare(
        "UPDATE couple_chapters SET image_id=:image,updated_by=:uid,updated_at=NOW() WHERE id=:id AND image_id IS NULL"
    );

    foreach ($chapters as [$seed, $order, $label, $title, $description]) {
        $findSeedChapter->execute([':seed'=>$seed]);
        $existing = $findSeedChapter->fetch(PDO::FETCH_ASSOC);
        if (!$existing) {
            // Migra itens exatos de versões anteriores sem destruir descrições que já tenham sido editadas.
            $findLegacyChapter->execute([':title'=>$title]);
            $legacy = $findLegacyChapter->fetch(PDO::FETCH_ASSOC);
            if ($legacy) {
                try {
                    $claimLegacyChapter->execute([
                        ':seed'=>$seed, ':image'=>$imageIds[$seed], ':uid'=>$uid, ':id'=>(int)$legacy['id']
                    ]);
                } catch (Throwable $e) {}
            } else {
                $insertChapter->execute([
                    ':ord'=>$order, ':label'=>$label, ':title'=>$title, ':description'=>$description,
                    ':image'=>$imageIds[$seed], ':seed'=>$seed, ':uid'=>$uid
                ]);
            }
        } elseif (empty($existing['image_id'])) {
            $attachMissingImage->execute([':image'=>$imageIds[$seed], ':uid'=>$uid, ':id'=>(int)$existing['id']]);
        }
    }

    $pdo->prepare('UPDATE couple_settings SET seed_version=2 WHERE id=1')->execute();
}
