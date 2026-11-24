// Contrôle de saisie côté navigateur (le contrôle PHP reste la vraie barrière)
document.querySelectorAll('form[data-validate]').forEach(f => {
  f.addEventListener('submit', e => {
    const v = n => (f.elements[n] ? f.elements[n].value.trim() : '');
    const pw = f.elements.password ? f.elements.password.value : '';
    const mail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const errs = [];
    const type = f.dataset.validate;

    if (type === 'login') {
      if (!v('email') || !pw) errs.push('Veuillez saisir votre email et votre mot de passe.');
      else if (!mail.test(v('email'))) errs.push('Adresse email invalide.');
    }
    if (type === 'register') {
      if (!v('username') || !v('email') || !pw) errs.push('Tous les champs sont obligatoires.');
      if (!/^[a-zA-Z0-9_\-]{3,50}$/.test(v('username'))) errs.push("Nom d'utilisateur : 3 à 50 caractères (lettres, chiffres, _ ou -).");
      if (!mail.test(v('email'))) errs.push('Adresse email invalide.');
      if (pw.length < 8) errs.push('Le mot de passe doit contenir au moins 8 caractères.');
      if (pw !== f.elements.password_confirm.value) errs.push('Les mots de passe ne correspondent pas.');
    }
    if (type === 'forgot') {
      if (!mail.test(v('email'))) errs.push('Adresse email invalide.');
    }
    if (type === 'reset') {
      if (pw.length < 8) errs.push('Le mot de passe doit contenir au moins 8 caractères.');
      if (pw !== f.elements.password_confirm.value) errs.push('Les mots de passe ne correspondent pas.');
    }

    if (errs.length) {
      e.preventDefault();
      const box = f.querySelector('[data-errors]');
      box.replaceChildren(...errs.map(m => Object.assign(document.createElement('div'), { textContent: m })));
      box.hidden = false;
    }
  });
});
