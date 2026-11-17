// Show/hide the sidebar
document.getElementById('menu-toggle').addEventListener('click', function () {
  var s = document.getElementById('sidebar');
  s.classList.toggle('hidden');
  s.classList.toggle('show');
});

// Show/hide the add forms
document.querySelectorAll('[data-toggle]').forEach(function (b) {
  b.addEventListener('click', function () {
    var t = document.querySelector(b.dataset.toggle);
    t.hidden = !t.hidden;
  });
});