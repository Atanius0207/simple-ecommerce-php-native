const slides = document.getElementById('slides');
const dotsContainer = document.getElementById('dots');
let currentIndex = 0;
const totalSlides = slides.children.length;

// Buat titik navigasi
for (let i = 0; i < totalSlides; i++) {
  const dot = document.createElement('span');
  if (i === 0) dot.classList.add('active');
  dot.addEventListener('click', () => goToSlide(i));
  dotsContainer.appendChild(dot);
}
const dots = dotsContainer.children;

function goToSlide(index) {
  currentIndex = index;
  slides.style.transform = `translateX(-${index * 100}%)`;
  for (let dot of dots) dot.classList.remove('active');
  dots[index].classList.add('active');
}

function nextSlide() {
  currentIndex = (currentIndex + 1) % totalSlides;
  goToSlide(currentIndex);
}

setInterval(nextSlide, 3000);
