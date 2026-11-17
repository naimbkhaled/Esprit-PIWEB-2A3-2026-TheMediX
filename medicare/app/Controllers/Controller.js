
class Controller {
  constructor(app) { this.app = app; }
  render(title, page, html) { Layout(this.app, title, page, html); scrollTo(0, 0); }
}
