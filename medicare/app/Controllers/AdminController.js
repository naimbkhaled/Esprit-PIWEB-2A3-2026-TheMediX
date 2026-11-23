class AdminController extends Controller {
  constructor() { super('admin'); }
  dashboard() { this.render('Dashboard', 'dashboard', V.dashboard()); }
  list(key)   { this.render(SCHEMA[key].label, key, table(key, true)); }
}
