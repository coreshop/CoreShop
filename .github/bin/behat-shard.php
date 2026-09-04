#!/usr/bin/env php
<?php

declare(strict_types=1);

/*
 * Splits the Behat suites of a profile into shards that can run on separate CI runners.
 *
 * The suites of a profile are declared one file per domain and filtered by tag, so they are the
 * natural unit to split by: every suite can run on its own with `behat --suite=…`. Their sizes
 * differ by two orders of magnitude, though (the cart suite alone holds more than a quarter of
 * all domain scenarios), so a round-robin split would leave one runner carrying the whole tail.
 * Each suite is therefore weighted by the number of scenarios its tag filter matches, and the
 * suites are then distributed longest-first onto the shard that is currently the emptiest.
 *
 * The scenario count is an approximation of the runtime, not a measurement of it: a scenario
 * outline counts once, and no scenario is more expensive than another. It is good enough to keep
 * the shards within a few percent of each other, and it needs no timing data to be carried
 * between runs.
 *
 * Usage:
 *   php .github/bin/behat-shard.php --shard=2 --shards=4
 *   php .github/bin/behat-shard.php --profile=ui --shards=4 --plan
 *
 * Prints the suite names of the requested shard to STDOUT, one per line, and the full
 * distribution to STDERR.
 */

use Symfony\Component\Yaml\Yaml;

require __DIR__ . '/../../vendor/autoload.php';

$options = getopt('', ['shard::', 'shards::', 'config::', 'profile::', 'features::', 'plan']);

$shards = (int) ($options['shards'] ?? 4);
$shard = (int) ($options['shard'] ?? 0);
$configFile = $options['config'] ?? __DIR__ . '/../../behat.yml.dist';
$profile = $options['profile'] ?? 'default';
$featuresDir = $options['features'] ?? __DIR__ . '/../../features';
$planOnly = array_key_exists('plan', $options);

if ($shards < 1) {
    fwrite(STDERR, "--shards must be at least 1\n");

    exit(1);
}

if (!$planOnly && ($shard < 1 || $shard > $shards)) {
    fwrite(STDERR, sprintf("--shard must be between 1 and %d, got %d\n", $shards, $shard));

    exit(1);
}

/**
 * Behat resolves the paths in `imports` relative to the directory of the file that imports them,
 * and merges what it loads recursively.
 */
$load = static function (string $file) use (&$load): array {
    if (!is_file($file)) {
        fwrite(STDERR, sprintf("Config file not found: %s\n", $file));

        exit(1);
    }

    $config = Yaml::parseFile($file) ?? [];
    $imports = $config['imports'] ?? [];
    unset($config['imports']);

    $merged = [];

    foreach ($imports as $import) {
        $merged = array_replace_recursive($merged, $load(dirname($file) . '/' . $import));
    }

    return array_replace_recursive($merged, $config);
};

$config = $load($configFile);

// Every Behat profile extends `default`, which is where all suites of this repository are
// declared — the other profiles only replace the extensions and the tag filter around them.
$settings = array_replace_recursive($config['default'] ?? [], $config[$profile] ?? []);

if (!isset($settings['suites'])) {
    fwrite(STDERR, sprintf("Profile \"%s\" declares no suites in %s\n", $profile, $configFile));

    exit(1);
}

$suites = $settings['suites'];
$profileFilter = $settings['gherkin']['filters']['tags'] ?? null;

/**
 * Collects the tags of every scenario below the features directory. Tags written above `Feature:`
 * belong to all of its scenarios, tags written above a scenario only to that one. A scenario
 * outline counts as a single scenario.
 *
 * @return list<array<string, true>>
 */
$scenarioTags = static function (string $directory): array {
    if (!is_dir($directory)) {
        fwrite(STDERR, sprintf("Features directory not found: %s\n", $directory));

        exit(1);
    }

    $files = new RegexIterator(
        new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)),
        '/\.feature$/',
    );

    $scenarios = [];

    foreach ($files as $file) {
        $featureTags = [];
        $pendingTags = [];

        foreach (file($file->getPathname()) as $line) {
            $line = trim($line);

            if (str_starts_with($line, '@')) {
                foreach (preg_split('/\s+/', $line) as $tag) {
                    $pendingTags[ltrim($tag, '@')] = true;
                }

                continue;
            }

            if (str_starts_with($line, 'Feature:')) {
                $featureTags = $pendingTags;
                $pendingTags = [];

                continue;
            }

            if (str_starts_with($line, 'Scenario:') || str_starts_with($line, 'Scenario Outline:')) {
                $scenarios[] = $featureTags + $pendingTags;
                $pendingTags = [];
            }
        }
    }

    return $scenarios;
};

/**
 * Evaluates a Behat tag filter the way Behat's own TagFilter does: `&&` separates conjunctions,
 * `,` alternatives within one conjunction, and a leading `~` negates a tag.
 *
 * @param array<string, true> $tags
 */
$matches = static function (string $expression, array $tags): bool {
    foreach (explode('&&', $expression) as $conjunct) {
        $satisfied = false;

        foreach (explode(',', $conjunct) as $tag) {
            $tag = trim($tag);
            $negated = str_starts_with($tag, '~');
            $tag = ltrim($tag, '~@');

            if (isset($tags[$tag]) !== $negated) {
                $satisfied = true;

                break;
            }
        }

        if (!$satisfied) {
            return false;
        }
    }

    return true;
};

$scenarios = $scenarioTags($featuresDir);

$weights = [];
$empty = [];

foreach ($suites as $name => $suite) {
    // Behat applies the profile's own filter on top of the suite's, so a suite only sees the
    // scenarios that satisfy both. That is what keeps the UI suites — which live in the same
    // `default` profile as the domain suites — empty for a `-p default` run.
    $filter = implode('&&', array_filter([$profileFilter, $suite['filters']['tags'] ?? null]));

    $weight = '' === $filter ? count($scenarios) : count(array_filter(
        $scenarios,
        static fn (array $tags): bool => $matches($filter, $tags),
    ));

    // Handing a suite without a single matching scenario to Behat costs a full kernel boot and
    // runs nothing, so leave it out of the plan — but say so, because a suite that unexpectedly
    // turns up here is a suite whose scenarios nobody is running.
    if (0 === $weight) {
        $empty[] = $name;

        continue;
    }

    $weights[$name] = $weight;
}

// Longest processing time first: the heaviest suite is placed first, every following suite goes
// onto whichever shard is the emptiest so far. Sorting by name where weights are equal keeps the
// distribution stable across runs, so all shards of one build agree on who runs what.
uksort($weights, static fn (string $a, string $b): int => [$weights[$b], $a] <=> [$weights[$a], $b]);

$plan = array_fill(1, $shards, []);
$totals = array_fill(1, $shards, 0);

foreach ($weights as $name => $weight) {
    $target = array_search(min($totals), $totals, true);

    $plan[$target][] = $name;
    $totals[$target] += $weight;
}

foreach ($plan as $index => $names) {
    fwrite(STDERR, sprintf(
        "Shard %d/%d — %d scenarios in %d suites: %s\n",
        $index,
        $shards,
        $totals[$index],
        count($names),
        implode(', ', $names),
    ));
}

fwrite(STDERR, sprintf(
    "%d scenarios in %d suites of profile \"%s\"; %d suites match nothing and are skipped: %s\n",
    array_sum($weights),
    count($weights),
    $profile,
    count($empty),
    implode(', ', $empty),
));

if ($planOnly) {
    exit(0);
}

echo implode("\n", $plan[$shard]), "\n";
