<?php
/**
 * The strip along the bottom of every page.
 *
 * Deliberately quiet. It carries the build version, because when someone
 * reports a problem the first useful question is which build they are on.
 */

declare(strict_types=1);

/** Bump when the schema or behaviour changes in a way that needs a redeploy. */
const APP_VERSION = '1.0.0';

function render_footer(): void
{
    ?>
    <footer class="footer">
        <p class="footer__left">
            <span class="footer__mark"><?= brand_mark(16) ?></span>
            © <?= e(date('Y')) ?> <?= e(APP_NAME) ?> · <?= e(APP_TAGLINE) ?>
        </p>
        <nav class="footer__links" aria-label="Footer links">
            <a href="mailto:registrar@hagmah.edu">Contact</a>
        </nav>
        <p class="footer__right">
            Build <span class="mono"><?= e(APP_VERSION) ?></span>
            <span class="footer__dot" aria-hidden="true">·</span>
            Records shown are demonstration data
        </p>
    </footer>
    <?php
}
