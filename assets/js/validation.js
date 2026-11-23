/* Contrôle de saisie en JavaScript (sans attributs HTML required/pattern). Il sera doublé côté PHP dans les Controllers. */
function err(id,msg){var e=document.getElementById("err-"+id),i=document.getElementById(id);if(e)e.textContent=msg||"";if(i)i.classList.toggle("is-invalid",!!msg);return !msg;}
function lenOk(id,min,max,label){var v=document.getElementById(id).value.trim();
  if(!v)return err(id,label+" est obligatoire.");
  if(v.length<min)return err(id,label+" : minimum "+min+" caractères.");
  if(v.length>max)return err(id,label+" : maximum "+max+" caractères.");
  return err(id,"");}
function validerGroupe(){var a=lenOk("nom",3,50,"Le nom"),b=lenOk("description",10,300,"La description"),c=lenOk("regles",10,500,"Les règles");return a&&b&&c;}
function validerInvitation(){var v=document.getElementById("invite").value.trim();
  if(!v)return err("invite","Saisissez un email ou un identifiant.");
  if(v.indexOf("@")>-1&&!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v))return err("invite","Email invalide.");
  if(v.length<3)return err("invite","Identifiant trop court.");
  return err("invite","");}
function validerRejet(){return lenOk("motif",5,200,"Le motif");}
function validerLogin(){var a=lenOk("login",3,30,"L’identifiant"),p=document.getElementById("mdp").value;
  var b=p.length>=6?err("mdp",""):err("mdp","Mot de passe : minimum 6 caractères.");
  return a&&b;} /* le PHP revérifie en base (password_verify) + session */
