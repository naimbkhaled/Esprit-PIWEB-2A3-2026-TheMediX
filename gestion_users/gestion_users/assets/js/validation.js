// Contrôle de saisie côté client (JavaScript, sans validation HTML5)
(function () {
  const form = document.getElementById('regForm');
  const doctorBox = document.getElementById('doctorFields');
  const nameRx = /^[A-Za-zÀ-ÿ][A-Za-zÀ-ÿ '\-]{1,49}$/;
  const emailRx = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
  const phoneRx = /^(\+216)?[0-9]{8}$/;

  const roleValue = () => form.querySelector('input[name="role"]:checked').value;
  const val = (id) => document.getElementById(id).value.trim();
  const setErr = (id, msg) => {
    form.querySelector('[data-for="' + id + '"]').textContent = msg || '';
    document.getElementById(id).classList.toggle('invalid', !!msg);
  };

  form.querySelectorAll('input[name="role"]').forEach((r) =>
    r.addEventListener('change', () => { doctorBox.hidden = roleValue() !== 'medecin'; })
  );

  function validate() {
    let ok = true;
    const check = (id, cond, msg) => { setErr(id, cond ? '' : msg); if (!cond) ok = false; };

    check('nom', nameRx.test(val('nom')), "Nom invalide (lettres uniquement, 2 à 50 caractères).");
    check('prenom', nameRx.test(val('prenom')), "Prénom invalide (lettres uniquement, 2 à 50 caractères).");

    const d = new Date(val('date_naissance'));
    const age = (Date.now() - d) / 31557600000;
    check('date_naissance', val('date_naissance') !== '' && !isNaN(d) && d <= new Date() && age <= 120,
          "Date de naissance invalide.");

    check('genre', val('genre') !== '', "Choisissez un genre.");
    check('email', emailRx.test(val('email')), "Adresse e-mail invalide.");
    check('telephone', phoneRx.test(val('telephone')), "Numéro invalide (8 chiffres, +216 accepté).");
    check('hebergement', val('hebergement').length <= 150, "Lieu d'hébergement trop long (150 max).");

    if (roleValue() === 'medecin') {
      check('specialite', nameRx.test(val('specialite')), "Spécialité invalide.");
      const f = document.getElementById('preuve_identite').files[0];
      let msg = '';
      if (!f) msg = "Ajoutez votre justificatif de médecin certifié.";
      else if (f.size > 5 * 1024 * 1024) msg = "Fichier supérieur à 5 Mo.";
      else if (!['application/pdf', 'image/jpeg', 'image/png'].includes(f.type)) msg = "Formats acceptés : PDF, JPG, PNG.";
      check('preuve_identite', msg === '', msg);
    }
    return ok;
  }

  form.addEventListener('submit', (e) => { if (!validate()) e.preventDefault(); });
})();
