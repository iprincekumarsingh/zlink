<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="qr_code_delete_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-fw fa-sm fa-trash-alt text-muted mr-2"></i>
                    <?= l('qr_code_delete_modal.header') ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="qr_code_delete_modal" method="post" action="<?= url('qr-codes/delete') ?>" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="qr_code_id" value="" />

                    <div class="notification-container"></div>

                    <p class="text-muted"><?= l('qr_code_delete_modal.subheader') ?></p>

                    <div class="mt-4">
                        <button type="submit" name="submit" class="btn btn-lg btn-block btn-danger"><?= l('global.delete') ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    /* On modal show load new data */
    $('#qr_code_delete_modal').on('show.bs.modal', event => {
        let qr_code_id = $(event.relatedTarget).data('qr-code-id');

        $(event.currentTarget).find('input[name="qr_code_id"]').val(qr_code_id);
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
