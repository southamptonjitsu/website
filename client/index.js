import './css/fonts.scss';
import './css/new.scss';
import './css/footer.scss';

import './css/standard.scss';

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
