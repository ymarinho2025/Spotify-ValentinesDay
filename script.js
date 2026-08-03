/* ============================================================
   PERSONALIZE AQUI
============================================================ */

const COUPLE = {
  startDate: '2026-07-16',
  firstMet: '2026-06-13',
  names: 'Sarah & Yuri',

  // Texto exibido no último slide da retrospectiva.
  finalMessage: 'Nossa história só está começando ✨',
};

/* ============================================================
   PLAYLIST COMPLETA
   Os arquivos MP3 precisam estar na mesma pasta do index.html
============================================================ */

const PLAYLIST = [
  {
    src: "music/Banda do Mar - Mais Ninguém (Videoclipe) - BandadoMarVEVO (youtube).mp3",
    title: "Mais Ninguém",
    artist: "Banda do mar",
    cover: "https://imgs.search.brave.com/ALi6ooIDqd-mN5WTcVrYYVvdrtWvyteZ7NyAAwTjxlw/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9pMS5z/bmRjZG4uY29tL2Fy/dHdvcmtzLTAwMDEw/MjM1NzQ5MC1hMmR5/amctdDEwODB4MTA4/MC5qcGc",
    text: `Eu só espero que não venha mais ninguém
Aí eu tenho você só pra mim
Roubo teu sono, quero teu tudo
Se mais alguém vier não vou notar

Preciso de você pra me fazer feliz
Não quero mais ficar aqui
Preciso me ver só pra me fazer maior
Mas quando você vem, eu fico melhor`
  },
  {
    src: "music/Esperando Na Janela - Cogumelo Plutão (youtube).mp3",
    title: "Esperando Na Janela",
    artist: "Cogumelo Plutão",
    cover: "https://imgs.search.brave.com/SmnwyO_RmHO1eOI11fQ-Ta62CMEAx52-IPRsD9u3KZo/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9zdGF0/aWMucW9idXouY29t/L2ltYWdlcy9jb3Zl/cnMvZ2MvdGgvaWh4/ZzBjNHBsdGhnY182/MDAuanBn",
    text: `Você é a escada da minha subida
Você é o amor da minha vida
É o meu abrir de olhos no amanhecer
Verdade que me leva a viver

Você é a espera na janela
A ave que vem de longe tão bela
A esperança que arde em calor
Você é a tradução do que é o amor `
  },
    {
    src: "music/Stephen Sanchez - Until I Found You (Official Video) - StephenSanchezVEVO (youtube).mp3",
    title: "Until I Found You",
    artist: "Stephen Sanchez",
    cover: "https://imgs.search.brave.com/pVB2syOTIc1Z4tGP-2soAW8UYHMP4hP-lVebfF0h68E/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9pLmRp/c2NvZ3MuY29tL28y/MWVYRFBQY1BRNy12/cURtVTg5WEJHUEN1/ajRjQmpNVG9nTUIz/dWRKMHMvcnM6Zml0/L2c6c20vcTo0MC9o/OjMwMC93OjMwMC9j/ek02THk5a2FYTmpi/MmR6L0xXUmhkR0Zp/WVhObExXbHQvWVdk/bGN5OVNMVEkxTVRn/NC9OalV5TFRFM016/RXdOemc0L056UXRP/VGt4TVM1d2JtYy5q/cGVn",
    text: `I would never fall in love again until I found her
I said I would never fall unless it's you I fall into
I was lost within the darkness, but then I found her
I found you`
  },
  {
    src: "music/Goo Goo Dolls – Iris [Official Music Video] [4K Remaster] - Goo Goo Dolls (youtube).mp3",
    title: "Iris",
    artist: "Goo Goo Dolls",
    cover: "https://imgs.search.brave.com/ejqq7lbynewS2VPT3jrjUCNjL5dGmPIt6h_ZvVh-PwQ/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9pLmRp/c2NvZ3MuY29tL2I0/QlNKclJEbzBJQ21L/VG5hOTR0a003Zk9v/cEdtSVdaY1o0WV9O/RVdHZ3cvcnM6Zml0/L2c6c20vcTo0MC9o/OjMwMC93OjMwMC9j/ek02THk5a2FYTmpi/MmR6L0xXUmhkR0Zp/WVhObExXbHQvWVdk/bGN5OVNMVEUxT0Rj/MC9OVE01TFRFMU9U/a3pPRGswL01UUXRN/alE1TWk1cWNHVm4u/anBlZw",
    text: `And I'd give up forever to touch you
Cause I know that you feel me somehow
You're the closest to heaven that I'll ever be
And I don't wanna go home right now`
  },
];

/* ============================================================
   COUPLE REWIND
============================================================ */

const REWIND_SLIDES = [
  {
    type: 'cover',
    cls: 'r-cover',
    eyebrow: 'A nossa retrospectiva',
    title: 'Couple\nRewind',
    sub: COUPLE.names,
  },
  {
    type: 'season',
    cls: 'r-season',
    eyebrow: 'Nossa Musica',
    title: '"Mais Ninguém"',
    sub: '',
    icon: '🎵',
  },
 {
  type: 'chapter',
  cls: 'r-chapter',
  chapter: 'O início de tudo',
  title: 'Tribo',
  text: 'Em meio a tantas pessoas, Deus permitiu que nossos caminhos se encontrassem e que uma linda história começasse a ser escrita.',
  photo: 'images/photo.jpg',
},

{
  type: 'chapter',
  cls: 'r-chapter',
  chapter: 'Capítulo 1',
  title: 'Gincana',
  text: 'Foi na gincana que pudemos nos aproximar ainda mais. Eu não estava tão animado para participar, até perceber que seria uma oportunidade de nos vermos e conversarmos.',
  photo: 'images/photo1.jpg',
},

{
  type: 'chapter',
  cls: 'r-chapter',
  chapter: 'Capítulo 2',
  title: 'Primeiro encontro',
  text: 'Passamos nosso primeiro final de semana inteiro juntos. Entre conversas, risadas e momentos simples, criamos lembranças que levaremos para sempre em nossos corações.',
  photo: 'images/photo2.jpg',
},
{
  type: 'chapter',
  cls: 'r-chapter',
  chapter: 'Capítulo 3',
  title: 'Passeando com a cara metade',
  text: 'Descrições por Amore Mio/Sarah 😜',
  photo: 'images/photo3.jpg',
},

{
  type: 'chapter',
  cls: 'r-chapter',
  chapter: 'Capítulo 4',
  title: 'Final de semana juntos',
  text: 'Descrições por Amore Mio/Sarah 😜',
  photo: 'images/photo4.jpg',
},
{
  type: 'chapter',
  cls: 'r-chapter',
  chapter: 'Capítulo 5',
  title: 'Cinema',
  text: 'Ao lado da minha Mary Jane. Mas, diferente do Peter Parker, essa história não vai acabar. 😜❤️',
  photo: 'images/photo5.jpg',
},
{
  type: 'chapter',
  cls: 'r-chapter',
  chapter: 'Capítulo 6',
  title: 'TE AMO SARAH',
  text: 'Descrições por Amore Mio/Sarah 😜',
  photo: 'images/photo6.jpg',
},
  {
    type: 'final',
    cls: 'r-final r-final--hours',
    eyebrow: 'Horas juntos',
    title: '',
    counter: true,
    message: COUPLE.finalMessage,
  },
];

const SLIDE_DURATION = 6000;

/* ============================================================
   CONTADORES
============================================================ */


function hoursTogether() {
  const start = new Date(`${COUPLE.startDate}T00:00:00`);
  const now = new Date();

  return Math.max(
    0,
    Math.floor((now - start) / (1000 * 60 * 60))
  );
}

function daysTogether() {
  const start = new Date(COUPLE.startDate);
  const now = new Date();

  return Math.max(
    0,
    Math.floor((now - start) / (1000 * 60 * 60 * 24))
  );
}

function daysSinceMet() {
  const start = new Date(COUPLE.firstMet);
  const now = new Date();

  return Math.max(
    0,
    Math.floor((now - start) / (1000 * 60 * 60 * 24))
  );
}

(function initCounters() {
  const daysTogetherEl = document.getElementById('daysTogether');
  const daysSinceMetEl = document.getElementById('daysSinceMet');

  if (daysTogetherEl) {
    daysTogetherEl.textContent = daysTogether().toLocaleString('pt-BR');
  }

  if (daysSinceMetEl) {
    daysSinceMetEl.textContent = daysSinceMet().toLocaleString('pt-BR');
  }
})();

/* ============================================================
   PLAYER REAL
============================================================ */

(function realPlayer() {
  const audio = document.getElementById('audio');
  const playBtn = document.getElementById('playBtn');
  const prevBtn = document.getElementById('prevBtn');
  const nextBtn = document.getElementById('nextBtn');
  const icon = document.getElementById('playIcon');
  const fill = document.getElementById('progressFill');
  const bar = document.getElementById('progressBar');
  const timeNow = document.getElementById('timeNow');
  const timeEnd = document.getElementById('timeEnd');
  const titleEl = document.getElementById('trackTitle');
  const artistEl = document.getElementById('trackArtist');
  const coverEl = document.getElementById('coverImg');

  if (!audio || !playBtn || !prevBtn || !nextBtn || !icon || !fill || !bar || !timeNow || !timeEnd || !titleEl || !artistEl || !coverEl) {
    return;
  }

  const PLAY_PATH = '<path d="M8 5v14l11-7z"/>';
  const PAUSE_PATH = '<path d="M6 5h4v14H6zM14 5h4v14h-4z"/>';

  let idx = 0;

  function fmt(seconds) {
    if (!isFinite(seconds) || isNaN(seconds)) {
      return '0:00';
    }

    seconds = Math.floor(seconds);

    const min = Math.floor(seconds / 60);
    const sec = String(seconds % 60).padStart(2, '0');

    return `${min}:${sec}`;
  }

  function load(index, autoplay = false) {

  idx = (index + PLAYLIST.length) % PLAYLIST.length;

  const music = PLAYLIST[idx];

  audio.src = music.src;

  titleEl.textContent = music.title;

  artistEl.textContent = music.artist;

  if (music.cover) {
    coverEl.src = music.cover;
  }

  /* =========================
     TEXTO / LETRA
  ========================= */

  const lyricsBox = document.querySelector('.lyrics__box');

  if (lyricsBox && music.text) {

    lyricsBox.innerHTML = `
      <p>${music.text}</p>
    `;
  }

  /* =========================
     RESET DA BARRA
  ========================= */

  fill.style.width = '0%';

  timeNow.textContent = '0:00';

  timeEnd.textContent = '0:00';

  /* =========================
     AUTOPLAY
  ========================= */

  if (autoplay) {

    audio.play().catch(() => {});

  }
}

  function togglePlay() {
    if (audio.paused) {
      audio.play().catch(() => {});
    } else {
      audio.pause();
    }
  }

  audio.addEventListener('play', () => {
    icon.innerHTML = PAUSE_PATH;
  });

  audio.addEventListener('pause', () => {
    icon.innerHTML = PLAY_PATH;
  });

  audio.addEventListener('loadedmetadata', () => {
    timeEnd.textContent = fmt(audio.duration);
  });

  audio.addEventListener('timeupdate', () => {
    if (!audio.duration) return;

    const percent = (audio.currentTime / audio.duration) * 100;

    fill.style.width = `${percent}%`;
    timeNow.textContent = fmt(audio.currentTime);
  });

  audio.addEventListener('ended', () => {
    load(idx + 1, true);
  });

  bar.addEventListener('click', (event) => {
    if (!audio.duration) return;

    const rect = bar.getBoundingClientRect();
    const ratio = (event.clientX - rect.left) / rect.width;

    audio.currentTime = ratio * audio.duration;
  });

  playBtn.addEventListener('click', togglePlay);

  prevBtn.addEventListener('click', () => {
    load(idx - 1, !audio.paused);
  });

  nextBtn.addEventListener('click', () => {
    load(idx + 1, !audio.paused);
  });

  load(0, false);
})();

/* ============================================================
   COUPLE REWIND
============================================================ */

(function rewind() {
  const overlay = document.getElementById('wrapped');
  const stage = document.getElementById('wrappedStage');
  const bars = document.getElementById('wrappedBars');
  const openBtn = document.getElementById('openWrapped');
  const closeBtn = document.getElementById('wrappedClose');
  const tapL = document.getElementById('tapLeft');
  const tapR = document.getElementById('tapRight');

  if (!overlay || !stage || !bars || !openBtn || !closeBtn || !tapL || !tapR) {
    return;
  }

  let idx = 0;
  let timeoutId = null;

  function buildSlide(slide) {
    const el = document.createElement('section');

    el.className = `slide ${slide.cls || ''}`;

    let inner = '';

    switch (slide.type) {
      case 'cover':
        inner = `
          <p class="r__eyebrow">${slide.eyebrow || ''}</p>
          <h2 class="r__title r__title--big">${(slide.title || '').replace(/\n/g, '<br>')}</h2>
          <p class="r__sub">${slide.sub || ''}</p>
          <div class="r__hint">toca pra avançar →</div>
        `;
        break;

      case 'season':
      case 'moon':
        inner = `
          <p class="r__eyebrow">${slide.eyebrow || ''}</p>
          <div class="r__icon">${slide.icon || ''}</div>
          <h2 class="r__title">${slide.title || ''}</h2>
          <p class="r__sub">${slide.sub || ''}</p>
        `;
        break;

      case 'chapter':
        inner = `
          <div class="r-chap">
            <div class="r-chap__photo" style="background-image:url('${slide.photo}')"></div>
            <div class="r-chap__body">
              <p class="r__eyebrow">${slide.chapter || ''}</p>
              <h2 class="r__title">${slide.title || ''}</h2>
              <p class="r__text">${slide.text || ''}</p>
            </div>
          </div>
        `;
        break;

      case 'final':
        inner = `
          <div class="r-final__particles" aria-hidden="true">
            <span></span><span></span><span></span><span></span><span></span>
          </div>

          <div class="r-final__ribbon r-final__ribbon--top" aria-hidden="true"></div>

          <div class="r-final__content">
            <p class="r-final__label">${slide.eyebrow || ''}</p>

            ${
              slide.counter
                ? `
              <div class="r-final__hours">
                <span class="r-final__number" data-counter-hours>0</span>
              </div>
            `
                : ''
            }

            <p class="r-final__message">${slide.message || ''}</p>
            <p class="r-final__names">${COUPLE.names}</p>
          </div>

          <div class="r-final__ribbon r-final__ribbon--bottom" aria-hidden="true"></div>
        `;
        break;
    }

    el.innerHTML = inner;

    return el;
  }

  REWIND_SLIDES.forEach((slide) => {
    stage.appendChild(buildSlide(slide));
  });

  REWIND_SLIDES.forEach(() => {
    const bar = document.createElement('div');

    bar.className = 'wbar';
    bar.innerHTML = '<span class="wbar__fill"></span>';

    bars.appendChild(bar);
  });

  const slides = stage.querySelectorAll('.slide');
  const wbars = bars.querySelectorAll('.wbar');

  function animateCounter(el, target) {
    const duration = 1800;
    const start = performance.now();

    function step(time) {
      const progress = Math.min(1, (time - start) / duration);
      const eased = 1 - Math.pow(1 - progress, 3);

      el.textContent = Math.floor(eased * target).toLocaleString('pt-BR');

      if (progress < 1) {
        requestAnimationFrame(step);
      }
    }

    requestAnimationFrame(step);
  }

  function showSlide(number) {
    if (number < 0) {
      number = 0;
    }

    if (number >= slides.length) {
      close();
      return;
    }

    idx = number;

    slides.forEach((slide, i) => {
      slide.classList.toggle('is-active', i === number);
    });

    wbars.forEach((bar, i) => {
      bar.classList.remove('is-active');
      bar.classList.toggle('is-done', i < number);

      const fill = bar.querySelector('.wbar__fill');
      fill.style.width = i < number ? '100%' : '0%';
    });

    const activeBar = wbars[number];

    activeBar.classList.add('is-active');
    activeBar.style.setProperty('--dur', `${SLIDE_DURATION}ms`);

    const counterTogether = slides[number].querySelector('[data-counter-together]');
    const counterMet = slides[number].querySelector('[data-counter-met]');
    const counterHours = slides[number].querySelector('[data-counter-hours]');

    if (counterTogether) {
      animateCounter(counterTogether, daysTogether());
    }

    if (counterMet) {
      animateCounter(counterMet, daysSinceMet());
    }

    if (counterHours) {
      animateCounter(counterHours, hoursTogether());
    }

    clearTimeout(timeoutId);

    timeoutId = setTimeout(() => {
      showSlide(idx + 1);
    }, SLIDE_DURATION);
  }

  function open() {
    overlay.classList.add('is-open');
    overlay.setAttribute('aria-hidden', 'false');

    document.body.style.overflow = 'hidden';

    showSlide(0);
  }

  function close() {
    overlay.classList.remove('is-open');
    overlay.setAttribute('aria-hidden', 'true');

    document.body.style.overflow = '';

    clearTimeout(timeoutId);
  }

  openBtn.addEventListener('click', open);

  closeBtn.addEventListener('click', close);

  tapL.addEventListener('click', () => {
    showSlide(idx - 1);
  });

  tapR.addEventListener('click', () => {
    showSlide(idx + 1);
  });

  document.addEventListener('keydown', (event) => {
    if (!overlay.classList.contains('is-open')) return;

    if (event.key === 'ArrowRight') {
      showSlide(idx + 1);
    }

    if (event.key === 'ArrowLeft') {
      showSlide(idx - 1);
    }

    if (event.key === 'Escape') {
      close();
    }
  });
})();