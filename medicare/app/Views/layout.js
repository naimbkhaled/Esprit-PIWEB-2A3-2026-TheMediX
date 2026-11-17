function Layout(app, title, cur, content) {
  document.title = title + ' | MediCare';
  document.getElementById('root').innerHTML = app === 'front'
    ? Partials.navbar(cur) + `<main>${content}</main>` + Partials.footer()
    : `<div class="admin">${Partials.sidebar(cur)}
         <div class="main">${Partials.header(title)}<main class="wrap">${content}</main>${Partials.footer()}</div>
       </div>`;
}
