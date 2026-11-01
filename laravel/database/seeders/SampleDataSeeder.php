<?php

namespace Database\Seeders;

use App\Enums\TaskPriority;
use App\Enums\UserGender;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Screenshot/demo dataset for the local dev database.
 *
 * Deliberately not called by DatabaseSeeder: production's php-fpm entrypoint runs
 * `migrate --seed --force` on every boot, so known-credential dev users must never
 * live in that call graph (see .ai/rules/seeders.md). Run it by hand instead:
 *
 *     php artisan db:seed --class=SampleDataSeeder
 */
class SampleDataSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the sample account and a dense, screenshot-ready dataset.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'username' => 'amirgjz',
            'email' => 'amirgjz@example.com',
            'firstname' => 'Amir',
            'lastname' => 'Ganj',
            'gender' => UserGender::Male,
            'country' => 'IR',
            'theme' => 'ocean',
            'locale' => 'en',
        ]);

        $categories = [];
        foreach (['Work', 'Personal', 'Health', 'Learning', 'Home'] as $name) {
            $categories[$name] = $user->categories()->create(['name' => $name]);
        }

        $plans = [
            1 => $user->plans()->create([
                'name' => 'Website Redesign',
                'description' => 'Rebuild the marketing site on the new brand system and ship it before the end of the quarter.',
                'start_date' => now()->subDays(21)->toDateString(),
                'finish_date' => now()->addDays(7)->toDateString(),
                'done' => false,
            ]),
            2 => $user->plans()->create([
                'name' => 'Q4 Reporting Sprint',
                'description' => 'Automate the quarterly metrics pipeline and ship the exec summary.',
                'start_date' => now()->subDays(10)->toDateString(),
                'finish_date' => now()->addDays(5)->toDateString(),
                'done' => false,
            ]),
            3 => $user->plans()->create([
                'name' => 'Marathon Training Block',
                'description' => 'Sixteen-week build-up toward the spring marathon, finished on schedule.',
                'start_date' => now()->subDays(35)->toDateString(),
                'finish_date' => now()->subDays(1)->toDateString(),
                'done' => true,
            ]),
            4 => $user->plans()->create([
                'name' => 'Home Office Setup',
                'description' => 'Desk, lighting, acoustic panels and the eternal cable mess.',
                'start_date' => now()->subDays(14)->toDateString(),
                'finish_date' => now()->addDays(14)->toDateString(),
                'done' => false,
            ]),
            5 => $user->plans()->create([
                'name' => 'Year-End Cleanup',
                'description' => 'Archive old projects, purge the inbox, back everything up.',
                'start_date' => now()->addDays(3)->toDateString(),
                'finish_date' => now()->addDays(17)->toDateString(),
                'done' => false,
            ]),
        ];

        /** @var array<int, array{0: int, 1: string, 2: string, 3: bool, 4: TaskPriority, 5: int, 6: string, 7: int|null}> $tasks */
        $tasks = [
            // [day offset, title, description, done, priority, minutes, category, plan]
            [-21, 'Redesign kickoff meeting notes', 'Agenda, decisions and open questions from the brand sync.', true, TaskPriority::High, 60, 'Work', 1],
            [-20, 'Audit the current sitemap', 'List every live URL and flag the pages that die in the migration.', true, TaskPriority::Medium, 90, 'Work', 1],
            [-18, 'Buy a standing desk mat', 'Compare anti-fatigue mats and order the firm one.', true, TaskPriority::Low, 20, 'Home', 4],
            [-17, 'Draft the brand moodboard', 'Three directions, one page each, ready for review.', true, TaskPriority::High, 120, 'Work', 1],
            [-15, 'Long run 18k', 'Easy pace along the river loop, fuel at kilometer ten.', true, TaskPriority::Medium, 110, 'Health', 3],
            [-14, 'Clear out the old closet', 'Donate, recycle, keep — three boxes and out.', true, TaskPriority::Low, 75, 'Home', null],
            [-13, 'Competitor teardown', 'Pricing, onboarding and feature gaps of the four closest tools.', true, TaskPriority::Medium, 90, 'Work', 1],
            [-11, 'Interval session 6x800m', 'Track workout with full recovery between reps.', true, TaskPriority::Medium, 55, 'Health', 3],
            [-10, 'Wireframe the pricing page', 'Three tiers, monthly/annual toggle, FAQ below the fold.', true, TaskPriority::High, 120, 'Work', 1],
            [-9, 'Set up the metrics dashboard', 'Wire the quarterly KPIs to the warehouse queries.', true, TaskPriority::High, 90, 'Work', 2],
            [-8, 'Read chapter 4 of Deep Work', 'Notes on attention residue in the notebook app.', true, TaskPriority::Low, 45, 'Learning', null],
            [-7, 'Long run 20k', 'Last big volume weekend before the taper starts.', true, TaskPriority::Medium, 125, 'Health', 3],
            [-6, 'Order acoustic panels', 'Six panels, warm grey, with mounting tape.', true, TaskPriority::Low, 25, 'Home', 4],
            [-6, 'Renew the domain name', 'The .com renews this week — do not let it lapse.', false, TaskPriority::High, 15, 'Personal', null],
            [-5, 'Collect the Q3 numbers', 'Revenue, churn and activation pulled into one sheet.', true, TaskPriority::High, 75, 'Work', 2],
            [-4, 'Yoga and mobility session', 'Hips and hamstrings, forty slow minutes.', true, TaskPriority::Low, 40, 'Health', null],
            [-3, 'Update the component library', 'New badge and dropdown variants land with docs.', true, TaskPriority::Medium, 105, 'Work', 1],
            [-2, 'Grocery run for the week', 'Meal plan first, list second, no impulse buys.', true, TaskPriority::Low, 35, 'Personal', null],
            [-2, 'Book the dentist', 'Six-month checkup — mornings only.', false, TaskPriority::Medium, 20, 'Health', null],
            [-1, 'Marathon taper long run 12k', 'Shakeout pace, new race shoes, bed early.', true, TaskPriority::Medium, 80, 'Health', 3],
            [0, 'Ship the pricing page copy', 'Final pass on headlines and the plan comparison table.', false, TaskPriority::High, 60, 'Work', 1],
            [0, 'Review PR: navigation refactor', 'Keyboard nav and the dropdown role split need a close look.', false, TaskPriority::Medium, 30, 'Work', 2],
            [0, 'Evening run 8k', 'Recovery shakeout around the neighborhood.', false, TaskPriority::Low, 45, 'Health', null],
            [1, 'Assemble the new desk', 'Cable tray and monitor arm included — budget the whole evening.', false, TaskPriority::Medium, 90, 'Home', 4],
            [1, 'Draft the exec summary outline', 'Headline metric, three supporting stories, one ask.', false, TaskPriority::High, 50, 'Work', 2],
            [2, 'Write the brand glossary', 'Voice, terms and the words we agreed to stop using.', false, TaskPriority::Low, 40, 'Work', 1],
            [3, 'Inbox zero pass', 'Archive, snooze, reply — under an hour or it waits.', false, TaskPriority::Low, 45, 'Personal', null],
            [3, 'Weekly long run 16k', 'Back to normal training volume after the race taper.', false, TaskPriority::Medium, 100, 'Health', null],
            [4, 'Prototype the dark-mode toggle', 'Theme tokens first, per-surface contrast second.', false, TaskPriority::Medium, 120, 'Work', 1],
            [5, 'Ship the metrics pipeline', 'Scheduled warehouse job plus the failure alerts.', false, TaskPriority::High, 180, 'Work', 2],
            [5, 'Deep Work chapter 5', 'Strategies for rituals and fixed-schedule productivity.', false, TaskPriority::Low, 45, 'Learning', null],
            [7, 'Final QA on the redesign', 'Cross-browser pass, link check and the accessibility sweep.', false, TaskPriority::High, 150, 'Work', 1],
            [8, 'Reset the backup drives', 'Verify the last snapshot actually restores.', false, TaskPriority::Low, 30, 'Home', null],
            [10, 'Start the year-end cleanup', 'Archive the finished projects and empty the downloads folder.', false, TaskPriority::Medium, 60, 'Personal', 5],
        ];

        foreach ($tasks as [$offset, $title, $description, $done, $priority, $minutes, $category, $plan]) {
            $user->tasks()->create([
                'title' => $title,
                'description' => $description,
                'task_date' => now()->addDays($offset)->toDateString(),
                'estimated_minutes' => $minutes,
                'priority' => $priority,
                'done' => $done,
                'day_before_alarm' => 0,
                'category_id' => $categories[$category]->id,
                'plan_id' => $plan === null ? null : $plans[$plan]->id,
            ]);
        }
    }
}
