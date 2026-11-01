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
    $isDashboard = $active === 'dashboard';
    ?>
    <header class="topbar">
        <button class="topbar__menu" type="button" data-sidebar-toggle aria-label="Open navigation" aria-expanded="false" aria-controls="sidebar" title="Toggle navigation">
            <?= icon('menu', 19) ?>
        </button>

        <div class="topbar__heading">
            <?php if (!$isDashboard): ?>
                <a class="topbar__back" href="<?= e(url('dashboard.php')) ?>" aria-label="Back to dashboard">
                    <?= icon('arrow-left', 18) ?>
                </a>
            <?php endif; ?>
            <div class="topbar__breadcrumbs" aria-label="Breadcrumb">
                <span class="topbar__section"><?= e($section) ?></span>
                <span class="topbar__sub">Registrar workspace</span>
            </div>
        </div>

        <div class="topbar__search" role="search">
            <span class="topbar__search-icon"><?= icon('search', 15) ?></span>
            <input type="search" placeholder="Search students, courses, records..." aria-label="Global search" data-topbar-search>
        </div>

        <div class="topbar__spacer"></div>

        <div class="topbar__actions">
            <button class="iconbtn iconbtn--ghost topbar__bell" type="button" aria-label="Notifications" title="System Notifications">
                <?= icon('bell', 17) ?>
                <span class="topbar__indicator"></span>
            </button>

            <button class="iconbtn iconbtn--ghost" type="button" data-theme-toggle
                    aria-label="Switch between light and dark" title="Switch between light and dark">
                <span class="iconbtn__sun"><?= icon('sun', 17) ?></span>
                <span class="iconbtn__moon"><?= icon('moon', 17) ?></span>
            </button>

            <details class="usermenu">
                <summary class="usermenu__trigger">
                    <span class="usermenu__avatar-wrap">
                        <?= render_avatar(
                            mb_substr((string) ($user['full_name'] ?? ''), 0, 1),
                            mb_substr(explode(' ', (string) ($user['full_name'] ?? ''))[1] ?? '', 0, 1)
                        ) ?>
                    </span>
                    <span class="usermenu__meta">
                        <span class="usermenu__name"><?= e($user['full_name'] ?? '') ?></span>
                        <span class="usermenu__sub"><?= e(humanize($user['role'] ?? 'Staff')) ?></span>
                    </span>
                    <span class="usermenu__chevron"><?= icon('chevron-down', 13) ?></span>
                </summary>
                <div class="usermenu__panel">
                    <div class="usermenu__identity">
                        <p class="usermenu__full"><?= e($user['full_name'] ?? '') ?></p>
                        <p class="usermenu__mail"><?= e($user['email'] ?? '') ?></p>
                        <div class="usermenu__badge"><?= badge(humanize($user['role'] ?? '')) ?></div>
                    </div>
                    <div class="usermenu__menu">
                        <a class="usermenu__item" href="<?= e(url('dashboard.php')) ?>">
                            <?= icon('dashboard', 15) ?><span>Dashboard</span>
                        </a>
                        <a class="usermenu__item" href="<?= e(url('students/index.php')) ?>">
                            <?= icon('student', 15) ?><span>Student Records</span>
                        </a>
                        <a class="usermenu__item" href="<?= e(url('courses/index.php')) ?>">
                            <?= icon('course', 15) ?><span>Course Catalog</span>
                        </a>
                        <?php if (can('users.manage')): ?>
                            <a class="usermenu__item" href="<?= e(url('users/index.php')) ?>">
                                <?= icon('shield', 15) ?><span>User Accounts</span>
                            </a>
                        <?php endif; ?>
                        <div class="usermenu__divider"></div>
                        <a class="usermenu__item usermenu__item--danger" href="<?= e(url('auth/logout.php')) ?>">
                            <?= icon('logout', 15) ?><span>Sign out</span>
                        </a>
                    </div>
                </div>
            </details>
        </div>
    </header>
    <?php
}
