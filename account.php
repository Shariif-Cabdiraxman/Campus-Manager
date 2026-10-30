<?php
/**
 * Account settings.
 *
 * A read-only summary of the signed-in account and what it can reach. The
 * password and role are managed by an administrator in the user module, so
 * this screen does not pretend to offer changes it cannot safely make.
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/layout.php';

require_login();

$me = current_user();

$account = db_row(
    'SELECT `id`, `username`, `email`, `full_name`, `role`, `status`, `last_login_at`, `created_at`
       FROM `users`
      WHERE `id` = ?
      LIMIT 1',
    'i',
    [(int) $me['id']]
);

if ($account === null) {
    not_found('Your account could not be found. Please sign in again.');
}

$access = [];
foreach (navigation_groups() as $group) {
    foreach ($group['items'] as $item) {
        if (!isset($item['manage']) || can($item['manage'])) {
            $access[] = ['label' => $item['label'], 'icon' => $item['icon'], 'href' => $item['href']];
        }
    }
}

layout_start('Account settings', 'dashboard');

render_page_head([
    'eyebrow'  => 'Account',
    'title'    => 'Account settings',
    'subtitle' => 'The identity and access attached to this session.',
    'meta'     => 'Signed in as ' . e($account['username']),
    'actions'  => '<a class="btn btn--quiet" href="' . e(url('dashboard.php')) . '">' . icon('arrow-left', 15) . '<span>Back to dashboard</span></a>',
]);
?>

<div class="split split--wide">
    <div class="stackgap">
        <section class="recordhead">
            <span class="recordhead__mark"><?= icon('student', 26) ?></span>
            <div class="recordhead__text">
                <h2 class="recordhead__title"><?= e($account['full_name']) ?></h2>
                <p class="recordhead__meta">
                    <span><?= e($account['username']) ?></span>
                    <span><?= e($account['email']) ?></span>
                    <span><?= role_badge($account['role']) ?></span>
                </p>
            </div>
        </section>

        <section class="panel">
            <div class="panel__head">
                <div class="panel__titles">
                    <h2 class="panel__title">Account details</h2>
                    <p class="panel__sub">Held by the registrar's office</p>
                </div>
            </div>
            <div class="panel__body">
                <?php render_deflist([
                    ['term' => 'Full name', 'desc' => e($account['full_name'])],
                    ['term' => 'Username', 'desc' => '<span class="table__id">' . e($account['username']) . '</span>'],
                    ['term' => 'Email', 'desc' => '<a class="strong" href="mailto:' . e($account['email']) . '">' . e($account['email']) . '</a>'],
                    ['term' => 'Role', 'desc' => role_badge($account['role'])],
                    ['term' => 'Status', 'desc' => badge($account['status'])],
                    ['term' => 'Last sign-in', 'desc' => e(format_datetime($account['last_login_at']))],
                    ['term' => 'Account created', 'desc' => e(format_datetime($account['created_at']))],
                    ['term' => 'Session', 'desc' => 'Protected by a one-hour idle timeout'],
                ]); ?>
            </div>
        </section>

        <section class="panel">
            <div class="panel__head">
                <div class="panel__titles">
                    <h2 class="panel__title">Password</h2>
                    <p class="panel__sub">Passwords are never shown or stored in plain text</p>
                </div>
            </div>
            <div class="panel__body">
                <p class="text-2 text-sm">
                    Your password is stored as a one-way bcrypt hash. Ask an administrator
                    to issue a new one from <strong>User accounts</strong> if you need to change it.
                </p>
            </div>
        </section>
    </div>

    <div class="stackgap">
        <section class="panel">
            <div class="panel__head">
                <div class="panel__titles">
                    <h2 class="panel__title">What you can open</h2>
                    <p class="panel__sub">Derived from your role</p>
                </div>
            </div>
            <div class="panel__body">
                <ul class="relatedlist">
                    <?php foreach ($access as $item): ?>
                        <li class="relatedlist__item">
                            <a class="relatedlist__link" href="<?= e(url($item['href'])) ?>">
                                <span class="relatedlist__label"><?= e($item['label']) ?></span>
                            </a>
                            <span class="relatedlist__trail"><?= icon($item['icon'], 15) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>

        <section class="panel">
            <div class="panel__head">
                <div class="panel__titles">
                    <h2 class="panel__title">Session</h2>
                </div>
            </div>
            <div class="panel__body">
                <p class="text-2 text-sm">Signing out ends this session on this device.</p>
                <a class="btn btn--danger mt-2" href="<?= e(url('auth/logout.php')) ?>">
                    <?= icon('logout', 15) ?><span>Sign out</span>
                </a>
            </div>
        </section>
    </div>
</div>

<?php layout_end(); ?>
