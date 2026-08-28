# Couple CMS v2

## Fluxo
- Página pública continua em `/`.
- Coração abre `/login-casal.php`.
- Primeiro usuário é criado em `/setup-casal.php`.
- No painel, `Convidar parceiro(a)` gera um link válido por 7 dias.
- O link abre `/convite-casal.php?token=...` e cria somente a segunda conta.
- O banco bloqueia uma terceira conta.

## Conteúdo original
A migração v2 reconhece e disponibiliza no painel:
- Sarah & Yuri
- 16/07/2026 (início do namoro)
- 13/06/2026 (quando se conheceram)
- texto original de “Sobre nós”
- mensagem final
- avatares `her.jpg` e `me.jpg`
- Tribo / `photo.jpg`
- Gincana / `photo1.jpg`
- Primeiro encontro / `photo2.jpg`
- Passeando com a cara metade / `photo3.jpg`
- Final de semana juntos / `photo4.jpg`
- Cinema / `photo5.jpg`
- TE AMO SARAH / `photo6.jpg`

A migração é feita apenas uma vez (`seed_version=2`). O botão “Restaurar itens originais faltantes” permite repetir conscientemente a verificação.

## Banco
Variáveis Vercel:
- `DATABASE_URL`
- `JWT_SECRET` (32+ caracteres)

Fotos novas enviadas pelo painel são armazenadas em Base64 no PostgreSQL/NeonDB. As imagens originais continuam referenciando os arquivos locais até serem substituídas, evitando enviar vários megabytes ao banco durante o primeiro acesso.
