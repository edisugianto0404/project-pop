const popup = document.getElementById('bonusPopup');
const openPopupButton = document.getElementById('openPopup');
const closePopupButton = document.getElementById('closePopup');
const spinButton = document.getElementById('spinButton');
const resultEl = document.getElementById('spinResult');
const canvas = document.getElementById('spinWheel');
const ctx = canvas.getContext('2d');

const segments = [
  '500.000',
  '200.000',
  '100.000',
  '50.000',
  '20.000',
  '10.000',
  'COBA LAGI',
  'COBA LAGI',
  'COBA LAGI',
];
const segmentColors = segments.map((segment) =>
  segment === 'COBA LAGI' ? '#f44336' : '#00e676'
);

let rotation = 0;
let isSpinning = false;

const popupState = {
  open() {
    popup.classList.add('show');
    popup.setAttribute('aria-hidden', 'false');
  },
  close() {
    popup.classList.remove('show');
    popup.setAttribute('aria-hidden', 'true');
  },
};

openPopupButton.addEventListener('click', () => popupState.open());
closePopupButton.addEventListener('click', () => popupState.close());
popup.addEventListener('click', (event) => {
  if (event.target === popup) {
    popupState.close();
  }
});

document.addEventListener('keydown', (event) => {
  if (event.key === 'Escape') {
    popupState.close();
  }
});

const segmentAngle = (Math.PI * 2) / segments.length;
const pointerAngle = Math.PI / 2;

function drawWheel() {
  const { width, height } = canvas;
  const radius = width / 2 - 8;
  ctx.clearRect(0, 0, width, height);

  segments.forEach((label, index) => {
    const startAngle = rotation + index * segmentAngle;
    const endAngle = startAngle + segmentAngle;
    ctx.beginPath();
    ctx.moveTo(width / 2, height / 2);
    ctx.arc(width / 2, height / 2, radius, startAngle, endAngle);
    ctx.closePath();
    ctx.fillStyle = segmentColors[index];
    ctx.fill();

    ctx.save();
    ctx.translate(width / 2, height / 2);
    ctx.rotate(startAngle + segmentAngle / 2);
    ctx.textAlign = 'right';
    ctx.fillStyle = '#002b18';
    ctx.font = 'bold 14px Poppins, sans-serif';
    ctx.fillText(label, radius - 12, 5);
    ctx.restore();
  });

  ctx.beginPath();
  ctx.arc(width / 2, height / 2, 18, 0, Math.PI * 2);
  ctx.fillStyle = '#fff';
  ctx.fill();
}

function easeOutCubic(t) {
  return 1 - Math.pow(1 - t, 3);
}

function spinTo(targetIndex) {
  const turns = 6;
  const targetRotation =
    pointerAngle - (targetIndex + 0.5) * segmentAngle + turns * Math.PI * 2;
  const startRotation = rotation;
  const delta = targetRotation - startRotation;
  const duration = 4200;
  const startTime = performance.now();

  function animate(time) {
    const elapsed = time - startTime;
    const progress = Math.min(elapsed / duration, 1);
    rotation = startRotation + delta * easeOutCubic(progress);
    drawWheel();

    if (progress < 1) {
      requestAnimationFrame(animate);
    } else {
      isSpinning = false;
      resultEl.textContent = 'Hasil spin: COBA LAGI';
    }
  }

  requestAnimationFrame(animate);
}

spinButton.addEventListener('click', async () => {
  if (isSpinning || spinButton.disabled) {
    return;
  }

  isSpinning = true;
  resultEl.textContent = 'Memproses spin...';

  try {
    const response = await fetch('spin.php', { method: 'POST' });
    const data = await response.json();

    if (data.status === 'blocked') {
      resultEl.textContent = 'SPIN SUDAH DIGUNAKAN';
      spinButton.textContent = 'SPIN SUDAH DIGUNAKAN';
      spinButton.disabled = true;
      isSpinning = false;
      return;
    }

    const cobaLagiIndices = segments
      .map((label, index) => (label === 'COBA LAGI' ? index : null))
      .filter((value) => value !== null);
    const targetIndex =
      cobaLagiIndices[Math.floor(Math.random() * cobaLagiIndices.length)];
    spinButton.disabled = true;
    spinButton.textContent = 'SPIN DIGUNAKAN';
    spinTo(targetIndex);
  } catch (error) {
    resultEl.textContent = 'Gagal melakukan spin. Coba lagi.';
    isSpinning = false;
  }
});

if (spinButton.dataset.used === '1') {
  resultEl.textContent = 'SPIN SUDAH DIGUNAKAN';
}

drawWheel();
