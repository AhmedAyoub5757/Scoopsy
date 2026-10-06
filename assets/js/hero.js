(function () {
  'use strict';

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  function initHero(hero) {
    var n = parseInt(hero.dataset.count, 10) || 1;
    var delay = reduceMotion ? 0 : (parseInt(hero.dataset.autoplay, 10) || 0);
    var half = Math.floor(n / 2);
    var current = 0;

    var cones = hero.querySelectorAll('.sc-cone');
    var bgs = hero.querySelectorAll('.sc-bg');
    var words = hero.querySelectorAll('.sc-word');
    var texts = hero.querySelectorAll('.sc-text');
    var props = hero.querySelectorAll('.sc-propset');
    var dots = hero.querySelectorAll('.sc-ind');

    hero.style.setProperty('--ap', delay + 'ms');

    /* ---------- Render the active flavor ---------- */
    function render() {
      cones.forEach(function (el, i) {
        var o = ((i - current + n + half) % n) - half; // -1, 0, 1 for 3 flavors
        el.style.setProperty('--o', o);
        el.style.setProperty('--a', i === current ? 1 : 0);
        el.classList.toggle('is-active', i === current);
      });
      [bgs, words, texts, props].forEach(function (list) {
        list.forEach(function (el, i) { el.classList.toggle('is-active', i === current); });
      });
      dots.forEach(function (el, i) {
        el.classList.toggle('is-active', i === current);
        el.setAttribute('aria-selected', i === current ? 'true' : 'false');
      });
    }

    /* ---------- Autoplay with pause/resume ---------- */
    var timer = null;
    var startedAt = 0;
    var remaining = delay;
    var reasons = {}; // hover, hidden, offscreen

    function isPaused() {
      return !!(reasons.hover || reasons.hidden || reasons.offscreen);
    }
    function schedule() {
      clearTimeout(timer);
      if (!delay || isPaused()) return;
      startedAt = Date.now();
      timer = setTimeout(function () { go(current + 1); }, remaining);
    }
    function restart() {
      remaining = delay;
      schedule();
    }
    function setPause(reason, value) {
      var was = isPaused();
      reasons[reason] = value;
      var now = isPaused();
      if (now && !was) {
        clearTimeout(timer);
        remaining = Math.max(0, remaining - (Date.now() - startedAt));
      } else if (!now && was) {
        schedule();
      }
      hero.classList.toggle('is-paused', now);
    }

    function go(index) {
      index = ((index % n) + n) % n;
      if (index === current) return;
      current = index;
      render();
      restart();
    }

    /* ---------- Controls ---------- */
    dots.forEach(function (dot) {
      dot.addEventListener('click', function () {
        go(parseInt(dot.dataset.index, 10));
      });
    });

    hero.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowRight') { go(current + 1); }
      else if (e.key === 'ArrowLeft') { go(current - 1); }
    });

    // Swipe (touch or mouse drag)
    var startX = null;
    hero.addEventListener('pointerdown', function (e) {
      if (e.pointerType === 'mouse' && e.button !== 0) return;
      startX = e.clientX;
    });
    hero.addEventListener('pointerup', function (e) {
      if (startX === null) return;
      var dx = e.clientX - startX;
      startX = null;
      if (Math.abs(dx) > 50) go(current + (dx < 0 ? 1 : -1));
    });
    hero.addEventListener('pointercancel', function () { startX = null; });

    /* ---------- Pause rules ---------- */
    hero.addEventListener('pointerenter', function (e) {
      if (e.pointerType === 'mouse') setPause('hover', true);
    });
    hero.addEventListener('pointerleave', function (e) {
      if (e.pointerType === 'mouse') setPause('hover', false);
    });
    document.addEventListener('visibilitychange', function () {
      setPause('hidden', document.hidden);
    });
    if ('IntersectionObserver' in window) {
      new IntersectionObserver(function (entries) {
        setPause('offscreen', !entries[0].isIntersecting);
      }, { threshold: 0.25 }).observe(hero);
    }

    render();
    requestAnimationFrame(function () { hero.classList.add('is-ready'); });
    schedule();
  }

  function boot() {
    document.querySelectorAll('.sc-hero').forEach(initHero);
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();