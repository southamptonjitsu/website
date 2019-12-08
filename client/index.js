import './css/new.scss';

import bgvid from './assets/jitsu-randoris-ethan.mp4';

(function loadVideo() {
    const video = document.getElementById('topvideo');

    if (!video) {
        window.requestAnimationFrame(loadVideo);
        return;
    }

    video.currentTime = 7;
    video.setAttribute('src', bgvid);
    video.style.display = 'block';
})();

const scroll = bar => boundary => function (current) {
    if (current >= boundary) {
        bar.classList.remove('hidden');
        return;
    }

    bar.classList.add('hidden');
};

window.addEventListener('scroll', function () {
    const bar = document.getElementById('topBar');
    const banner = document.getElementById('banner');
    scroll(bar)(banner.offsetHeight)(window.scrollY);
});
