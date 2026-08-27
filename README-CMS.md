# Spotify-ValentinesDay + Login Casal

## O que foi adicionado
- Site público continua em `index.html`.
- `login-casal.php`: acesso privado do casal.
- `casal.php`: painel para editar nomes, datas, texto Sobre nós, mensagem final, capítulos, descrições e fotos.
- Fotos novas são validadas e salvas em Base64 no PostgreSQL/NeonDB.
- `api/couple-public.php`: entrega o conteúdo publicado ao site.
- `api/couple-image.php`: entrega as imagens do banco.
- autenticação com senha segura, cookie JWT assinado, CSRF e auditoria.

## Configuração
1. Crie um banco PostgreSQL/NeonDB.
2. Configure `DATABASE_URL` e `JWT_SECRET` no ambiente (veja `.env.example`).
3. Abra `/setup-casal.php` uma única vez. O sistema cria o primeiro usuário e importa o conteúdo atual como seed.
4. Depois use `/login-casal.php`.

## Observação de deploy
O projeto agora precisa de hospedagem com PHP 8+ e PostgreSQL. No Vercel, use um runtime PHP compatível com as rotas PHP do projeto, ou mantenha o mesmo padrão de runtime PHP usado no InvestigationZ.
