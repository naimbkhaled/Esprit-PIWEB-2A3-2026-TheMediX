// ===== THE MEDIX — interactions premium =====
// Three.js (fond 3D), GSAP (animations), Lenis (défilement fluide)
(function () {
  var html = document.documentElement;
  if (localStorage.getItem('theme') === 'dark') html.setAttribute('data-theme', 'dark');

  // --- Bouton mode clair / sombre (même logique qu'avant) ---
  var bouton = document.getElementById('theme-toggle');
  if (bouton) bouton.addEventListener('click', function () {
    var dark = html.getAttribute('data-theme') !== 'dark';
    html.setAttribute('data-theme', dark ? 'dark' : 'light');
    localStorage.setItem('theme', dark ? 'dark' : 'light');
    if (window.medixScene) window.medixScene.setTheme(dark);
    if (window.gsap) gsap.fromTo('body', { opacity: .85 }, { opacity: 1, duration: .5 });
  });

  // --- Lenis : défilement fluide ---
  if (window.Lenis) {
    var lenis = new Lenis({ lerp: 0.09 });
    (function raf(t) { lenis.raf(t); requestAnimationFrame(raf); })(0);
  }

  // --- Three.js : nuage de particules + molécule flottante ---
  if (window.THREE && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
    var canvas = document.createElement('canvas'); canvas.id = 'bg3d'; document.body.prepend(canvas);
    var renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });
    renderer.setPixelRatio(Math.min(devicePixelRatio, 2));
    var scene = new THREE.Scene();
    var camera = new THREE.PerspectiveCamera(55, 1, 0.1, 100); camera.position.z = 9;

    var N = 900, pos = new Float32Array(N * 3);
    for (var i = 0; i < N * 3; i++) pos[i] = (Math.random() - .5) * 22;
    var g = new THREE.BufferGeometry(); g.setAttribute('position', new THREE.BufferAttribute(pos, 3));
    var pMat = new THREE.PointsMaterial({ size: .045, transparent: true, opacity: .7 });
    var points = new THREE.Points(g, pMat); scene.add(points);

    var blob = new THREE.Mesh(new THREE.IcosahedronGeometry(2.2, 2),
      new THREE.MeshBasicMaterial({ wireframe: true, transparent: true, opacity: .18 }));
    blob.position.set(4.5, 1.2, -2); scene.add(blob);
    var ring = new THREE.Mesh(new THREE.TorusGeometry(3.2, .012, 8, 140),
      new THREE.MeshBasicMaterial({ transparent: true, opacity: .35 }));
    ring.position.copy(blob.position); ring.rotation.x = 1.2; scene.add(ring);

    function setTheme(dark) {
      var a = new THREE.Color(dark ? '#c4a8ff' : '#1fb5c4'), b = new THREE.Color(dark ? '#7b52c7' : '#0f1b3d');
      pMat.color = a; blob.material.color = b; ring.material.color = a;
    }
    setTheme(html.getAttribute('data-theme') === 'dark');
    window.medixScene = { setTheme: setTheme };

    function resize() { renderer.setSize(innerWidth, innerHeight, false); camera.aspect = innerWidth / innerHeight; camera.updateProjectionMatrix(); }
    addEventListener('resize', resize); resize();
    var mx = 0, my = 0;
    addEventListener('pointermove', function (e) { mx = e.clientX / innerWidth - .5; my = e.clientY / innerHeight - .5; });
    (function loop(t) {
      t *= .001;
      points.rotation.y = t * .03; points.rotation.x = Math.sin(t * .1) * .1;
      blob.rotation.x = t * .15; blob.rotation.y = t * .2; ring.rotation.z = t * .1;
      camera.position.x += (mx * 1.5 - camera.position.x) * .04;
      camera.position.y += (-my * 1.5 - camera.position.y) * .04;
      camera.lookAt(0, 0, 0);
      renderer.render(scene, camera); requestAnimationFrame(loop);
    })(0);
  }

  // --- GSAP : loader, entrées et compteurs ---
  var spinner = document.getElementById('spinner');
  function intro() {
    if (!window.gsap) { if (spinner) spinner.style.display = 'none'; return; }
    var tl = gsap.timeline();
    if (spinner) tl.to(spinner, { opacity: 0, duration: .5, onComplete: function () { spinner.style.display = 'none'; } });
    tl.from('.navbar, .sidebar, .topbar', { y: -20, opacity: 0, duration: .7, stagger: .1, ease: 'power3.out' }, '-=.2')
      .from('.hero > *, .page-title, .reveal', { y: 30, opacity: 0, duration: .8, stagger: .08, ease: 'power3.out' }, '-=.4')
      .from('table tr, .message, .card, .feature', { y: 16, opacity: 0, duration: .5, stagger: .05, ease: 'power2.out' }, '-=.5');
    document.querySelectorAll('[data-count]').forEach(function (el) {
      var o = { v: 0 }; gsap.to(o, { v: +el.dataset.count, duration: 1.6, delay: .6, ease: 'power2.out', onUpdate: function () { el.textContent = Math.round(o.v); } });
    });
    // boutons magnétiques
    document.querySelectorAll('.btn').forEach(function (b) {
      b.addEventListener('pointermove', function (e) { var r = b.getBoundingClientRect(); gsap.to(b, { x: (e.clientX - r.left - r.width / 2) * .25, y: (e.clientY - r.top - r.height / 2) * .25, duration: .3 }); });
      b.addEventListener('pointerleave', function () { gsap.to(b, { x: 0, y: 0, duration: .5, ease: 'elastic.out(1,.4)' }); });
    });
  }
  if (document.readyState === 'complete') intro(); else addEventListener('load', intro);
})();
