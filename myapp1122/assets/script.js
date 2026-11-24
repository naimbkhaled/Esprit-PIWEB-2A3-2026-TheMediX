const t=document.getElementById('t');
t.onclick=()=>{
  const d=document.body.classList.toggle('d');
  t.textContent=d?'☀ Clair':'☾ Sombre';
};
