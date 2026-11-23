(function(){var t=localStorage.getItem("hc-theme")||"light";document.documentElement.setAttribute("data-theme",t);document.documentElement.setAttribute("data-bs-theme",t);})();
document.addEventListener("DOMContentLoaded",function(){
  var b=document.getElementById("themeToggle");if(!b)return;
  function label(){var t=document.documentElement.getAttribute("data-theme");b.innerHTML=t==="dark"?'<i class="bi bi-sun"></i> Mode clair':'<i class="bi bi-moon-stars"></i> Mode sombre';}
  b.addEventListener("click",function(){var t=document.documentElement.getAttribute("data-theme")==="dark"?"light":"dark";
    document.documentElement.setAttribute("data-theme",t);document.documentElement.setAttribute("data-bs-theme",t);localStorage.setItem("hc-theme",t);label();});
  label();});
