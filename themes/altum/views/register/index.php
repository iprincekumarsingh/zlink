<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <div class="d-flex flex-column align-items-center">
        <div class="col-xs-12 col-sm-10 col-md-8 col-lg-5">
            <?= \Altum\Alerts::output_alerts() ?>

            <div class="card border-0">
                <div class="card-body p-5">
                    <h4 class="card-title"><?= l('register.header') ?></h4>

                    <form action="" method="post" class="mt-4" role="form">
                        <div class="form-group">
                            <label for="name"><?= l('register.form.name') ?></label>
                            <input id="name" type="text" name="name" class="form-control <?= \Altum\Alerts::has_field_errors('name') ? 'is-invalid' : null ?>" value="<?= $data->values['name'] ?>" maxlength="32" placeholder="<?= l('register.form.name_placeholder') ?>" required="required" autofocus="autofocus" />
                            <?= \Altum\Alerts::output_field_error('name') ?>
                        </div>

                        <div class="form-group">
                            <label for="email"><?= l('register.form.email') ?></label>
                            <input id="email" type="email" name="email" class="form-control <?= \Altum\Alerts::has_field_errors('email') ? 'is-invalid' : null ?>" value="<?= $data->values['email'] ?>" maxlength="128" placeholder="<?= l('register.form.email_placeholder') ?>" required="required" />
                            <?= \Altum\Alerts::output_field_error('email') ?>
                        </div>

                        <div class="form-group">
                            <label for="password"><?= l('register.form.password') ?></label>
                            <input id="password" type="password" name="password" class="form-control <?= \Altum\Alerts::has_field_errors('password') ? 'is-invalid' : null ?>" value="<?= $data->values['password'] ?>" placeholder="<?= l('register.form.password_placeholder') ?>" required="required" />
                            <?= \Altum\Alerts::output_field_error('password') ?>
                        </div>

                        <?php if(settings()->captcha->register_is_enabled): ?>
                        <div class="form-group">
                            <?php $data->captcha->display() ?>
                        </div>
                        <?php endif ?>

                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="accept" class="custom-control-input" id="accept" required="required">
                            <label class="custom-control-label" for="accept">
                                <small class="text-muted">
                                    <?= sprintf(
                                        l('register.form.accept'),
                                        '<a href="' . settings()->main->terms_and_conditions_url . '" target="_blank">' . l('global.terms_and_conditions') . '</a>',
                                        '<a href="' . settings()->main->privacy_policy_url . '" target="_blank">' . l('global.privacy_policy') . '</a>'
                                    ) ?>
                                </small>
                            </label>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" name="submit" class="btn btn-primary btn-block"><?= l('register.form.register') ?></button>
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
                    </form>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="login" role="button"><?= l('register.login') ?></a>
        </div>
    </div>
</div>



