<?php
/**
 * The bar across the top of the shell.
 *
 * Carries only what belongs here: a way back into the navigation on small
 * screens, where you are, the theme control, and the account menu. Search and
 * filters live with the list they apply to, not here, because a global search
 * across eight different schemas would have to be a separate feature.
 */

declare(strict_types=1);

/** The human name of the current section, used in the bar and on the page. */
function current_section_label(string $active): string
{
    foreach (navigation_groups() as $group) {
        foreach ($group['items'] as $item) {
            if ($item['key'] === $active) {
                return $item['label'];
            }
        }
    }

    return 'Dashboard';
}

function render_topbar(string $active = ''): void
{
    $user    = current_user();
    $section = current_section_label($active);

    // A compact preview of the notice board. A failing query must never take
    // the top bar down with it, so an empty list is the fallback.
    $notices = [];
    try {
        $notices = db_all(
            'SELECT `id`, `title`, `published_at`
               FROM `announcements`
              WHERE `status` = \'published\'
              ORDER BY `published_at` DESC
              LIMIT 3'
        );
    } catch (Throwable $exception) {
        $notices = [];
    }
    ?>
    <header class="topbar">
        <button class="topbar__menu" type="button" data-sidebar-open aria-label="Open navigation" aria-controls="sidebar">
            <?= icon('menu', 19) ?>
        </button>

        <p class="topbar__section"><?= e($section) ?></p>

        <form class="topbar__search" method="get" action="<?= e(url('search.php')) ?>" role="search">
            <?= icon('search', 15) ?>
            <input type="search" name="q" value="<?= e(query_string('q')) ?>"
                   placeholder="Search anything…" aria-label="Search students, staff, courses and notices"
                   autocomplete="off">
        </form>

        <div class="topbar__actions">
            <button class="iconbtn iconbtn--ghost" type="button" data-theme-toggle
                    aria-label="Switch between light and dark" title="Switch between light and dark">
                <span class="iconbtn__sun"><?= icon('sun', 16) ?></span>
                <span class="iconbtn__moon"><?= icon('moon', 16) ?></span>
            </button>

            <details class="notifmenu">
                <summary class="topbar__bell" aria-label="Notifications">
                    <?= icon('bell', 16) ?>
                    <?php if ($notices !== []): ?><span class="topbar__dot" aria-hidden="true"></span><?php endif; ?>
                </summary>
                <div class="notifmenu__panel">
                    <div class="notifmenu__head">
                        <p class="notifmenu__title">Notice board</p>
                        <a class="notifmenu__link" href="<?= e(url('announcements/index.php')) ?>">View all</a>
                    </div>
                    <?php if ($notices === []): ?>
                        <p class="notifmenu__meta" style="padding:14px 10px">No published notices yet.</p>
                    <?php else: ?>
                        <?php foreach ($notices as $notice): ?>
                            <a class="notifmenu__item" href="<?= e(url('announcements/view.php?id=' . (int) $notice['id'])) ?>">
                                <span class="notifmenu__icon"><?= icon('announcement', 14) ?></span>
                                <span class="notifmenu__text">
                                    <span class="notifmenu__name"><?= e($notice['title']) ?></span>
                                    <span class="notifmenu__meta"><?= e(time_ago($notice['published_at'])) ?></span>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </details>

            <a class="topbar__settings" href="<?= e(url('account.php')) ?>" aria-label="Account settings" title="Account settings">
                <?= icon('settings', 16) ?>
            </a>
        </div>

        <details class="usermenu">
            <summary class="usermenu__trigger">
                <?= render_avatar(
                    mb_substr((string) ($user['full_name'] ?? ''), 0, 1),
                    mb_substr(explode(' ', (string) ($user['full_name'] ?? ''))[1] ?? '', 0, 1)
                ) ?>
                <span class="usermenu__identitytext">
                    <span class="usermenu__name"><?= e($user['full_name'] ?? '') ?></span>
                    <span class="usermenu__role"><?= e(humanize($user['role'] ?? '')) ?></span>
                </span>
                <?= icon('chevron-down', 13) ?>
            </summary>
            <div class="usermenu__panel">
                <div class="usermenu__identity">
                    <p class="usermenu__full"><?= e($user['full_name'] ?? '') ?></p>
                    <p class="usermenu__mail"><?= e($user['email'] ?? '') ?></p>
                    <p class="usermenu__badge"><?= badge(humanize($user['role'] ?? '')) ?></p>
                </div>
                <a class="usermenu__item" href="<?= e(url('dashboard.php')) ?>">
                    <?= icon('dashboard', 15) ?><span>Dashboard</span>
                </a>
                <a class="usermenu__item" href="<?= e(url('account.php')) ?>">
                    <?= icon('settings', 15) ?><span>Account settings</span>
                </a>
                <?php if (can('users.manage')): ?>
                    <a class="usermenu__item" href="<?= e(url('users/index.php')) ?>">
                        <?= icon('shield', 15) ?><span>User accounts</span>
                    </a>
                <?php endif; ?>
                <a class="usermenu__item usermenu__item--danger" href="<?= e(url('auth/logout.php')) ?>">
                    <?= icon('logout', 15) ?><span>Sign out</span>
                </a>
            </div>
        </details>
    </header>
    <?php
}
