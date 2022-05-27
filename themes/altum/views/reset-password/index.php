<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <div class="d-flex flex-column align-items-center">
        <div class="col-xs-12 col-sm-10 col-md-8 col-lg-5">
            <?= \Altum\Alerts::output_alerts() ?>

            <div class="card border-0">
                <div class="card-body p-5">
                    <h1 class="h4 card-title"><?= l('reset_password.header') ?></h1>
                    <p class="text-muted"><?= l('reset_password.subheader') ?></p>

                    <form action="" method="post" class="mt-4" role="form">
                        <div class="form-group">
                            <label for="new_password"><?= l('reset_password.new_password') ?></label>
                            <input id="new_password" type="password" name="new_password" class="form-control <?= \Altum\Alerts::has_field_errors('new_password') ? 'is-invalid' : null ?>" required="required" autofocus="autofocus" />
                            <?= \Altum\Alerts::output_field_error('new_password') ?>
                        </div>

                        <div class="form-group">
                            <label for="repeat_password"><?= l('reset_password.repeat_password') ?></label>
                            <input id="repeat_password" type="password" name="repeat_password" class="form-control <?= \Altum\Alerts::has_field_errors('repeat_password') ? 'is-invalid' : null ?>" required="required" />
                            <?= \Altum\Alerts::output_field_error('repeat_password') ?>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" name="submit" class="btn btn-primary btn-block my-1"><?= l('reset_password.submit') ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
