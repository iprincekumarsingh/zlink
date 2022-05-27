<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <div class="d-flex flex-column align-items-center">
        <div class="col-xs-12 col-sm-10 col-md-8 col-lg-5">
            <?= \Altum\Alerts::output_alerts() ?>

            <div class="card border-0">
                <div class="card-body p-5">
                    <h4 class="card-title"><?= l('login.header') ?></h4>

                    <form action="" method="post" class="mt-4" role="form">
                        <div class="form-group">
                            <label for="email"><?= l('login.form.email') ?></label>
                            <input id="email" type="text" name="email" class="form-control <?= \Altum\Alerts::has_field_errors('email') ? 'is-invalid' : null ?>" placeholder="<?= l('login.form.email_placeholder') ?>" value="<?= $data->values['email'] ?>" required="required" autofocus="autofocus" />
                            <?= \Altum\Alerts::output_field_error('email') ?>
                        </div>

                        <div class="form-group">
                            <label for="password"><?= l('login.form.password') ?></label>
                            <input id="password" type="password" name="password" class="form-control <?= \Altum\Alerts::has_field_errors('password') ? 'is-invalid' : null ?>" placeholder="<?= l('login.form.password_placeholder') ?>" value="<?= $data->user ? $data->values['password'] : null ?>" required="required" />
                            <?= \Altum\Alerts::output_field_error('password') ?>
                        </div>

                        <?php if($data->user && $data->user->twofa_secret && $data->user->status == 1): ?>
                            <div class="form-group">
                                <label for="twofa_token"><?= l('login.form.twofa_token') ?></label>
                                <input id="twofa_token" type="text" name="twofa_token" class="form-control <?= \Altum\Alerts::has_field_errors('twofa_token') ? 'is-invalid' : null ?>" placeholder="<?= l('login.form.twofa_token_placeholder') ?>" required="required" autocomplete="off" />
                                <?= \Altum\Alerts::output_field_error('twofa_token') ?>
                            </div>
                        <?php endif ?>

                        <?php if(settings()->captcha->login_is_enabled): ?>
                        <div class="form-group">
                            <?php $data->captcha->display() ?>
                        </div>
                        <?php endif ?>

                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="rememberme" class="custom-control-input" id="rememberme">
                            <label class="custom-control-label" for="rememberme"><small class="text-muted"><?= l('login.form.remember_me') ?></small></label>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" name="submit" class="btn btn-primary btn-block my-1"><?= l('login.form.login') ?></button>
                        </div>

                        <?php if(settings()->facebook->is_enabled || settings()->google->is_enabled || settings()->twitter->is_enabled || settings()->discord->is_enabled): ?>
                            <hr class="border-gray-100 my-3" />

                            <div class="">
                                <?php if(settings()->facebook->is_enabled): ?>
                                    <div class="mt-2">
                                        <a href="<?= url('login/facebook-initiate') ?>" class="btn btn-light btn-block">
                                            <img src="<?= ASSETS_FULL_URL . 'images/facebook.svg' ?>" class="mr-1" />
                                            <?= l('login.display.facebook') ?>
                                        </a>
                                    </div>
                                <?php endif ?>
                                <?php if(settings()->google->is_enabled): ?>
                                    <div class="mt-2">
                                        <a href="<?= url('login/google-initiate') ?>" class="btn btn-light btn-block">
                                            <img src="<?= ASSETS_FULL_URL . 'images/google.svg' ?>" class="mr-1" />
                                            <?= l('login.display.google') ?>
                                        </a>
                                    </div>
                                <?php endif ?>
                                <?php if(settings()->twitter->is_enabled): ?>
                                    <div class="mt-2">
                                        <a href="<?= url('login/twitter-initiate') ?>" class="btn btn-light btn-block">
                                            <img src="<?= ASSETS_FULL_URL . 'images/twitter.svg' ?>" class="mr-1" />
                                            <?= l('login.display.twitter') ?>
                                        </a>
                                    </div>
                                <?php endif ?>
                                <?php if(settings()->discord->is_enabled): ?>
                                    <div class="mt-2">
                                        <a href="<?= url('login/discord-initiate') ?>" class="btn btn-light btn-block">
                                            <img src="<?= ASSETS_FULL_URL . 'images/discord.svg' ?>" class="mr-1" />
                                            <?= l('login.display.discord') ?>
                                        </a>
                                    </div>
                                <?php endif ?>
                            </div>
                        <?php endif ?>

                        <div class="mt-4 text-center">
                            <small>
                                <a href="lost-password" class="text-muted" role="button"><?= l('login.display.lost_password') ?></a>
                                <?php if(settings()->users->email_confirmation): ?>
                                / <a href="resend-activation" class="text-muted" role="button"><?= l('login.display.resend_activation') ?></a>
                                <?php endif ?>
                            </small>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php if(settings()->users->register_is_enabled): ?>
            <div class="text-center mt-4">
                <?= sprintf(l('login.display.register'), '<a href="' . url('register') . '" class="font-weight-bold">' . l('login.display.register_help') . '</a>') ?></a>
            </div>
        <?php endif ?>
    </div>
</div>
