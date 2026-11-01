<?php
/**
 * Charts.
 *
 * Drawn as ordinary HTML and CSS rather than a charting library: a labelled
 * bar is a div with a width, which keeps the numbers in the page, needs no
 * external script, and prints properly. assets/js/charts.js only animates the
 * widths into place.
 *
 * The rule followed throughout: a value is always written next to its bar, so
 * the chart is never the only place a number appears, and length carries the
 * comparison while colour carries the category.
 */

declare(strict_types=1);

/**
 * A horizontal bar chart.
 *
 * @param array<int,array{label:string,value:int|float,note?:string,href?:string,tone?:string}> $rows
 * @param array{max?:int|float,unit?:string,noun?:string,empty:string} $options
 */
function render_bar_chart(array $rows, array $options = []): void
{
    $max = $options['max'] ?? null;
    $unit = $options['unit'] ?? '';
    $noun = $options['noun'] ?? '';

    if ($rows === []) {
        render_empty_state('grades', 'Nothing to chart yet', $options['empty'] ?? 'There is no data to show here yet.');

        return;
    }

    if ($max === null) {
        $max = 0;
        foreach ($rows as $row) {
            $max = max($max, (float) $row['value']);
        }
        $max = max($max, 1);
    }
    ?>
    <div class="chart" data-chart data-chart-max="<?= e((string) $max) ?>">
        <?php foreach ($rows as $row): ?>
            <?php
            $tone = $row['tone'] ?? '';
            $width = max(0, min(100, ((float) $row['value'] / (float) $max) * 100));
            $label = isset($row['href'])
                ? '<a href="' . e($row['href']) . '">' . e($row['label']) . '</a>'
                : e($row['label']);
            ?>
            <div class="chart__row<?= $tone !== '' ? ' chart__row--' . e($tone) : '' ?>">
                <span class="chart__label" title="<?= e($row['label']) ?>"><?= $label ?></span>
                <span class="chart__track">
                    <span class="chart__bar" data-chart-bar data-value="<?= e((string) $row['value']) ?>"
                          style="width: <?= e(number_format($width, 2)) ?>%"></span>
                </span>
                <span class="chart__value">
                    <?= e(number_format((float) $row['value'])) ?><?= e($unit) ?>
                    <?php if (!empty($row['note'])): ?>
                        <small><?= e($row['note']) ?></small>
                    <?php endif; ?>
                </span>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if ($noun !== ''): ?>
        <p class="chart__foot">
            <span><?= e(ucfirst(rtrim($noun, 's'))) ?>s by <?= e($noun === 'student' ? 'faculty' : 'category') ?></span>
            <span><?= e(number_format((float) $max)) ?> max</span>
        </p>
    <?php endif; ?>
    <?php
}

/** A labelled SVG area chart for monthly activity. */
function render_area_chart(array $rows, string $label = 'Monthly activity'): void
{
    if ($rows === []) {
        render_empty_state('enrollment', 'No activity yet', 'New activity will appear here when it is recorded.');

        return;
    }

    $width = 720;
    $top = 18;
    $bottom = 172;
    $left = 38;
    $right = 700;
    $max = max(1, max(array_map(static fn(array $row): int => (int) $row['n'], $rows)));
    $count = count($rows);
    $points = [];

    foreach ($rows as $index => $row) {
        $x = $count === 1 ? ($left + $right) / 2 : $left + (($right - $left) * $index / ($count - 1));
        $y = $bottom - (((int) $row['n'] / $max) * ($bottom - $top));
        $points[] = [round($x, 1), round($y, 1), (int) $row['n'], (string) $row['month_label']];
    }

    $line = implode(' ', array_map(static fn(array $point, int $index): string =>
        ($index === 0 ? 'M ' : 'L ') . $point[0] . ' ' . $point[1], $points, array_keys($points)));
    $first = $points[0];
    $last = $points[count($points) - 1];
    $area = $line . ' L ' . $last[0] . ' ' . $bottom . ' L ' . $first[0] . ' ' . $bottom . ' Z';
    $tooltipX = min($right - 84, max($left, $last[0] - 42));
    $tooltipY = max(2, $last[1] - 33);
    ?>
    <div class="area-chart" role="img" aria-label="<?= e($label) ?>: <?php
        echo e(implode(', ', array_map(static fn(array $row): string => $row['month_label'] . ' ' . $row['n'], $rows)));
    ?>">
        <svg viewBox="0 0 <?= e((string) $width) ?> 220" preserveAspectRatio="none" aria-hidden="true" focusable="false">
            <defs>
                <linearGradient id="activity-fill" x1="0" x2="0" y1="0" y2="1">
                    <stop offset="0%" stop-color="var(--mint-400)" stop-opacity=".34" />
                    <stop offset="100%" stop-color="var(--mint-300)" stop-opacity=".02" />
                </linearGradient>
            </defs>
            <?php for ($grid = 0; $grid < 4; $grid++): ?>
                <?php $gridY = $top + (($bottom - $top) * $grid / 3); ?>
                <line class="area-chart__grid" x1="<?= e((string) $left) ?>" y1="<?= e((string) $gridY) ?>" x2="<?= e((string) $right) ?>" y2="<?= e((string) $gridY) ?>" />
            <?php endfor; ?>
            <path class="area-chart__fill" d="<?= e($area) ?>" />
            <path class="area-chart__line" d="<?= e($line) ?>" />
            <?php foreach ($points as $point): ?>
                <circle class="area-chart__point" cx="<?= e((string) $point[0]) ?>" cy="<?= e((string) $point[1]) ?>" r="3.5" />
            <?php endforeach; ?>
            <circle class="area-chart__focus" cx="<?= e((string) $last[0]) ?>" cy="<?= e((string) $last[1]) ?>" r="6" />
            <g class="area-chart__tooltip" transform="translate(<?= e((string) $tooltipX) ?> <?= e((string) $tooltipY) ?>)">
                <rect width="84" height="25" rx="7" />
                <text x="42" y="16" text-anchor="middle"><?= e(number_format($last[2])) ?> · <?= e($last[3]) ?></text>
            </g>
            <?php foreach ($points as $point): ?>
                <text class="area-chart__axis" x="<?= e((string) $point[0]) ?>" y="205" text-anchor="middle"><?= e(substr($point[3], 0, 3)) ?></text>
            <?php endforeach; ?>
        </svg>
    </div>
    <?php
}

/** A grade donut with the pass rate in the center and an accessible legend. */
function render_grade_donut(array $distribution, int $total, float $passRate): void
{
    if ($total === 0) {
        render_empty_state('grades', 'No grades recorded', 'Marks entered against enrollments will be summarised here.');

        return;
    }

    $palette = ['A' => 'var(--grade-a)', 'B' => 'var(--grade-b)', 'C' => 'var(--grade-c)', 'D' => 'var(--grade-d)', 'F' => 'var(--grade-f)'];
    $stops = [];
    $cursor = 0.0;
    foreach ($distribution as $row) {
        if ($row['count'] <= 0) { continue; }
        $next = $cursor + (($row['count'] / $total) * 100);
        $color = $palette[$row['letter']] ?? 'var(--text-3)';
        $stops[] = $color . ' ' . number_format($cursor, 2, '.', '') . '% ' . number_format($next, 2, '.', '') . '%';
        $cursor = $next;
    }
    ?>
    <div class="grade-overview">
        <div class="grade-donut" style="--grade-ring: conic-gradient(<?= e(implode(', ', $stops)) ?>)"
             role="img" aria-label="Grade distribution across <?= e(number_format($total)) ?> marks; pass rate <?= e(number_format($passRate, 1)) ?> percent">
            <div class="grade-donut__center">
                <strong><?= e(number_format($passRate, 1)) ?>%</strong>
                <span>Pass rate</span>
            </div>
        </div>
        <ul class="grade-legend" aria-label="Grade counts and percentages">
            <?php foreach ($distribution as $row): ?>
                <?php if ($row['count'] <= 0) { continue; } ?>
                <li class="grade-legend__item grade-legend__item--<?= e(strtolower($row['letter'])) ?>">
                    <span class="grade-legend__key"><?= e($row['letter']) ?></span>
                    <span class="grade-legend__count"><?= e(number_format($row['count'])) ?></span>
                    <span class="grade-legend__share"><?= e(number_format(($row['count'] / $total) * 100, 1)) ?>%</span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
}

/** Compact rounded columns for the same monthly activity series. */
function render_column_chart(array $rows, int $total): void
{
    if ($rows === []) {
        render_empty_state('enrollment', 'No enrollments yet', 'Monthly counts will appear once enrollments are recorded.');

        return;
    }

    $max = max(1, max(array_map(static fn(array $row): int => (int) $row['n'], $rows)));
    ?>
    <div class="column-chart" role="img" aria-label="Monthly enrollment counts: <?php
        echo e(implode(', ', array_map(static fn(array $row): string => $row['month_label'] . ' ' . $row['n'], $rows)));
    ?>">
        <p class="column-chart__total"><?= e(number_format($total)) ?><span>enrollments recorded</span></p>
        <div class="column-chart__bars" aria-hidden="true">
            <?php foreach ($rows as $row): ?>
                <?php $height = max(8, (int) round(((int) $row['n'] / $max) * 100)); ?>
                <div class="column-chart__column<?= (int) $row['n'] === $max ? ' is-peak' : '' ?>" title="<?= e($row['month_label']) ?>: <?= e((string) $row['n']) ?>">
                    <span class="column-chart__value"><?= e((string) $row['n']) ?></span>
                    <span class="column-chart__track"><i style="height: <?= e((string) $height) ?>%"></i></span>
                    <span class="column-chart__label"><?= e(substr((string) $row['month_label'], 0, 3)) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

/**
 * The grade distribution, as one stacked bar plus a legend with the counts.
 *
 * @param array<int,array{letter:string,count:int,avg:?float}> $distribution
 */
function render_grade_distribution(array $distribution, int $total): void
{
    $tones = ['A' => 'ok', 'B' => 'info', 'C' => 'warn', 'D' => 'warn', 'F' => 'danger'];
    $swatches = ['A' => 'ok', 'B' => 'info', 'C' => 'warn', 'D' => 'warn', 'F' => 'danger'];

    if ($total === 0) {
        render_empty_state('grades', 'No grades recorded', 'Marks entered against enrollments will be summarised here.');

        return;
    }
    ?>
    <div class="stack" role="img"
         aria-label="Distribution of <?= e((string) $total) ?> recorded grades: <?php
            $parts = [];
            foreach ($distribution as $row) {
                $parts[] = $row['letter'] . ' ' . $row['count'] . ' ('
                    . round(($row['count'] / $total) * 100) . '%)';
            }
            echo e(implode(', ', $parts));
         ?>">
        <?php foreach ($distribution as $row): ?>
            <?php if ($row['count'] === 0) { continue; } ?>
            <span class="stack__seg swatch--<?= e($swatches[$row['letter']] ?? 'neutral') ?>"
                  style="width: <?= e(number_format(($row['count'] / $total) * 100, 3)) ?>%"
                  title="<?= e($row['letter']) ?>: <?= e((string) $row['count']) ?>"></span>
        <?php endforeach; ?>
    </div>

    <div class="legend">
        <?php foreach ($distribution as $row): ?>
            <span class="legend__item">
                <span class="legend__swatch swatch--<?= e($swatches[$row['letter']] ?? 'neutral') ?>"></span>
                <strong><?= e($row['letter']) ?></strong>
                <span class="text-3"><?= e((string) $row['count']) ?></span>
                <?php if ($row['avg'] !== null): ?>
                    <span class="text-3">avg <?= e(number_format((float) $row['avg'], 1)) ?></span>
                <?php endif; ?>
            </span>
        <?php endforeach; ?>
    </div>
    <?php
    unset($tones);
}

/**
 * A short list of key/value figures, used under the dashboard charts.
 *
 * @param array<string,array{value:string,label:string,tone?:string}> $figures
 */
function render_figures(array $figures): void
{
    ?>
    <dl class="deflist deflist--single">
        <?php foreach ($figures as $figure): ?>
            <div>
                <dt class="deflist__term"><?= e($figure['label']) ?></dt>
                <dd class="deflist__desc">
                    <span class="strong"><?= e($figure['value']) ?></span>
                    <?php if (!empty($figure['tone'])): ?>
                        <?= badge($figure['tone']) ?>
                    <?php endif; ?>
                </dd>
            </div>
        <?php endforeach; ?>
    </dl>
    <?php
}
