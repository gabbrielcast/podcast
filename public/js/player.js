window.onload = () => {
  let player = document.getElementById("player");
  let progress = document.getElementById("progress");
  let playerbtn = document.getElementById("playbtn");
  let current = document.getElementById("current");

  playerbtn.onclick = () => {
    if (player.paused) {
      player.play();
    } else {
      player.pause();
    }
  };

  player.onplay = () => {
    playerbtn.classList.remove("fa-play");
    playerbtn.classList.add("fa-pause");
  };
  player.onpause = () => {
    playerbtn.classList.add("fa-play");
    playerbtn.classList.remove("fa-pause");
  };

  player.ontimeupdate = () => {
    let ct = player.currentTime;
    current.innerHTML = timeFormat(ct);
    let duration = player.duration;
    let prog = Math.floor((ct * 100) / duration);
    progress.style.setProperty("--progress", prog + "%");
  };

  function timeFormat(ct) {
    minutes = Math.floor(ct / 60);
    seconds = Math.floor(ct % 60);

    if (seconds < 10) {
      seconds = "0" + seconds;
    }

    return minutes + ":" + seconds;
  }
};
