# Nosso Spotify do Amor 💚

Página estilo Spotify para o seu namoro, com **player real**, sobre o casal, letra de música e uma **retrospectiva Couple Rewind** estilo Stories.

## Como personalizar

### 1. Músicas (player com prev/next)
Coloque os arquivos de áudio na MESMA pasta do `index.html`:

- `musica1.mp3`
- `musica2.mp3`
- `musica3.mp3`

> Se forem `.mp4`, troque a extensão dentro do `script.js` (campo `src`).

No topo do `script.js`, edite o array `PLAYLIST` para mudar **título, artista e capa** de cada música:

```js
const PLAYLIST = [
  { src: 'musica1.mp3', title: 'Nosso Tema', artist: 'Eu & Você', cover: 'foto1.jpg' },
  { src: 'musica2.mp3', title: '...',         artist: '...',       cover: 'foto2.jpg' },
  { src: 'musica3.mp3', title: '...',         artist: '...',       cover: 'foto3.jpg' },
];
```

A capa muda automaticamente conforme a música tocando. Os botões **⏮ ⏯ ⏭** funcionam.

### 2. Data, nomes e textos
- `script.js` → `COUPLE.startDate` e `COUPLE.names`
- `index.html` → seção "Sobre o Casal" e "Letra"

### 3. Couple Rewind (retrospectiva)
Edite o array `REWIND_SLIDES` no `script.js`:
- **Capítulos**: troque `photo`, `title`, `text`
- **Estação / Lua**: troque `title`, `sub`, `icon`
- O slide final tem **contador automático de dias juntos**.

## Rodar
Abra o `index.html` no navegador. Pronto.
