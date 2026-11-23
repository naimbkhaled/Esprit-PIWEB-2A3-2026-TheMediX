<div class="modal fade" id="rejet" tabindex="-1"><div class="modal-dialog">
  <form class="modal-content" action="<?= e(url('admin-reject')) ?>" method="post" onsubmit="return validerRejet()" novalidate>
    <?= csrf_field() ?><input type="hidden" name="id" id="rejet-id" value="">
    <div class="modal-header"><h2 class="modal-title h5">Rejeter le groupe</h2><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><label class="form-label" for="motif">Motif du rejet</label><textarea id="motif" name="motif" rows="3" class="form-control"></textarea><div class="invalid-feedback" id="err-motif"></div></div>
    <div class="modal-footer"><button class="btn btn-danger">Confirmer le rejet</button></div>
  </form></div></div>
