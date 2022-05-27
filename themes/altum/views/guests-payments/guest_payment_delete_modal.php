<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="guest_payment_delete_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-fw fa-sm fa-trash-alt text-muted mr-2"></i>
                    <?= l('guest_payment_delete_modal.header') ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="guest_payment_delete" method="post" action="<?= url('payment-processors/delete') ?>" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="guest_payment_id" value="" />

                    <p class="text-muted"><?= l('guest_payment_delete_modal.subheader') ?></p>

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
    'use strict';

    /* On modal show load new data */
    $('#guest_payment_delete_modal').on('show.bs.modal', event => {
        let guest_payment_id = $(event.relatedTarget).data('guest-payment-id');
        $(event.currentTarget).find('input[name="guest_payment_id"]').val(guest_payment_id);
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
