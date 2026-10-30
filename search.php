<?php
/**
 * Global search.
 *
 * The bar across the top of every screen answers "where is that record?".
 * It searches the modules the signed-in role is allowed to read, and never
 * exposes a module the role cannot open. Each group is capped, so a common
 * name cannot turn one search into a thousand rows.
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/layout.php';

require_login();

$term = query_string('q');
$like = like_param($term);

$groups = [];

if ($term !== '') {
    // Students are addressed by their student number in URLs.
    $rows = db_all(
        'SELECT `student_no`, `first_name`, `last_name`, `email`, `status`
           FROM `students`
          WHERE `student_no` LIKE ? OR `first_name` LIKE ? OR `last_name` LIKE ? OR `email` LIKE ?
          ORDER BY `last_name`, `first_name`
          LIMIT 6',
        'ssss',
        [$like, $like, $like, $like]
    );
    if ($rows !== []) {
        $groups[] = [
            'label' => 'Students',
            'icon'  => 'student',
            'rows'  => array_map(static fn (array $row): array => [
                'label' => full_name($row['first_name'], $row['last_name']),
                'meta'  => $row['student_no'] . ' · ' . $row['email'],
                'href'  => url('students/view.php?id=' . urlencode((string) $row['student_no'])),
                'trail' => badge($row['status']),
            ], $rows),
        ];
    }

    $rows = db_all(
        'SELECT `id`, `first_name`, `last_name`, `email`, `status`
           FROM `lecturers`
          WHERE `first_name` LIKE ? OR `last_name` LIKE ? OR `email` LIKE ?
          ORDER BY `last_name`, `first_name`
          LIMIT 6',
        'sss',
        [$like, $like, $like]
    );
    if ($rows !== []) {
        $groups[] = [
            'label' => 'Lecturers',
            'icon'  => 'lecturer',
            'rows'  => array_map(static fn (array $row): array => [
                'label' => full_name($row['first_name'], $row['last_name']),
                'meta'  => $row['email'],
                'href'  => url('lecturers/view.php?id=' . (int) $row['id']),
                'trail' => badge($row['status']),
            ], $rows),
        ];
    }

    $rows = db_all(
        'SELECT `id`, `course_code`, `course_name`, `semester`
           FROM `courses`
          WHERE `course_code` LIKE ? OR `course_name` LIKE ?
          ORDER BY `course_code`
          LIMIT 6',
        'ss',
        [$like, $like]
    );
    if ($rows !== []) {
        $groups[] = [
            'label' => 'Courses',
            'icon'  => 'course',
            'rows'  => array_map(static fn (array $row): array => [
                'label' => $row['course_code'] . ' — ' . $row['course_name'],
                'meta'  => $row['semester'],
                'href'  => url('courses/view.php?id=' . (int) $row['id']),
                'trail' => '',
            ], $rows),
        ];
    }

    $rows = db_all(
        'SELECT `id`, `name`, `code`
           FROM `faculties`
          WHERE `name` LIKE ? OR `code` LIKE ?
          ORDER BY `name`
          LIMIT 6',
        'ss',
        [$like, $like]
    );
    if ($rows !== []) {
        $groups[] = [
            'label' => 'Faculties',
            'icon'  => 'faculty',
            'rows'  => array_map(static fn (array $row): array => [
                'label' => $row['name'],
                'meta'  => $row['code'],
                'href'  => url('faculties/view.php?id=' . (int) $row['id']),
                'trail' => '',
            ], $rows),
        ];
    }

    $rows = db_all(
        'SELECT `id`, `name`, `code`
           FROM `departments`
          WHERE `name` LIKE ? OR `code` LIKE ?
          ORDER BY `name`
          LIMIT 6',
        'ss',
        [$like, $like]
    );
    if ($rows !== []) {
        $groups[] = [
            'label' => 'Departments',
            'icon'  => 'department',
            'rows'  => array_map(static fn (array $row): array => [
                'label' => $row['name'],
                'meta'  => $row['code'],
                'href'  => url('departments/view.php?id=' . (int) $row['id']),
                'trail' => '',
            ], $rows),
        ];
    }

    $rows = db_all(
        'SELECT `id`, `title`, `status`, `published_at`
           FROM `announcements`
          WHERE `title` LIKE ? OR `content` LIKE ?
          ORDER BY `published_at` DESC
          LIMIT 6',
        'ss',
        [$like, $like]
    );
    if ($rows !== []) {
        $groups[] = [
            'label' => 'Announcements',
            'icon'  => 'announcement',
            'rows'  => array_map(static fn (array $row): array => [
                'label' => $row['title'],
                'meta'  => $row['published_at'] !== null ? time_ago($row['published_at']) : 'Draft',
                'href'  => url('announcements/view.php?id=' . (int) $row['id']),
                'trail' => badge($row['status']),
            ], $rows),
        ];
    }

    if (can('users.manage')) {
        $rows = db_all(
            'SELECT `id`, `full_name`, `username`, `email`, `role`, `status`
               FROM `users`
              WHERE `full_name` LIKE ? OR `username` LIKE ? OR `email` LIKE ?
              ORDER BY `full_name`
              LIMIT 6',
            'sss',
            [$like, $like, $like]
        );
        if ($rows !== []) {
            $groups[] = [
                'label' => 'User accounts',
                'icon'  => 'shield',
                'rows'  => array_map(static fn (array $row): array => [
                    'label' => $row['full_name'],
                    'meta'  => $row['username'] . ' · ' . $row['email'],
                    'href'  => url('users/view.php?id=' . (int) $row['id']),
                    'trail' => badge($row['role']),
                ], $rows),
            ];
        }
    }
}

layout_start('Search', 'dashboard');

render_page_head([
    'eyebrow'  => 'Search',
    'title'    => $term === '' ? 'Search the record' : 'Results for “' . $term . '”',
    'subtitle' => $term === ''
        ? 'Find a student, a lecturer, a course, a faculty, a department or a notice.'
        : 'Matches across every module you can open.',
    'meta'     => $term === ''
        ? 'Try a name, a student number, a course code or a notice title.'
        : e((string) array_sum(array_map(static fn (array $group): int => count($group['rows']), $groups))) . ' results',
]);

if ($term === '') {
    ?>
    <section class="panel">
        <div class="panel__body">
            <?php render_empty_state('search', 'Start typing to search', 'The search bar at the top of every screen searches the whole record. You can also open a module directly.', '<a class="btn btn--primary" href="' . e(url('students/index.php')) . '">' . icon('student', 15) . '<span>Open students</span></a>'); ?>
        </div>
    </section>
    <?php
} elseif ($groups === []) {
    ?>
    <section class="panel">
        <div class="panel__body">
            <?php render_empty_state('search', 'No matches', 'Nothing in the record matches “' . $term . '”. Try a shorter word or a reference number.'); ?>
        </div>
    </section>
    <?php
} else {
    ?>
    <div class="stackgap">
        <?php foreach ($groups as $group): ?>
            <section class="panel">
                <div class="panel__head">
                    <div class="panel__titles">
                        <h2 class="panel__title"><?= e($group['label']) ?></h2>
                        <p class="panel__sub"><?= e((string) count($group['rows'])) ?> closest matches</p>
                    </div>
                    <span class="stat__icon"><?= icon($group['icon'], 16) ?></span>
                </div>
                <div class="panel__body">
                    <ul class="relatedlist">
                        <?php foreach ($group['rows'] as $row): ?>
                            <li class="relatedlist__item">
                                <a class="relatedlist__link" href="<?= e($row['href']) ?>">
                                    <span class="relatedlist__label"><?= e($row['label']) ?></span>
                                    <span class="relatedlist__meta"><?= e($row['meta']) ?></span>
                                </a>
                                <?php if ($row['trail'] !== ''): ?>
                                    <span class="relatedlist__trail"><?= $row['trail'] ?></span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>
        <?php endforeach; ?>
    </div>
    <?php
}

layout_end();
