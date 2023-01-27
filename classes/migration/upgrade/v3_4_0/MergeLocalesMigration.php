<?php

/**
 * @file classes/migration/upgrade/v3_4_0/MergeLocalesMigration.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2000-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class MergeLocalesMigration
 *
 * @brief Change Locales from locale_countryCode localization folder notation to locale localization folder notation
 */

namespace APP\migration\upgrade\v3_4_0;

use Illuminate\Support\Facades\DB;
use PKP\install\DowngradeNotSupportedException;

class MergeLocalesMigration extends \PKP\migration\upgrade\v3_4_0\MergeLocalesMigration
{
    protected string $CONTEXT_TABLE = 'journals';
    protected string $CONTEXT_SETTINGS_TABLE = 'journal_settings';
    protected string $CONTEXT_COLUMN = 'journal_id';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        parent::up();

        // Those should not be here - I added them here just for demonstration purposes
        // journals


        // issue_galleys
        $issueGalleys = DB::table('issue_galleys')
            ->get();

        foreach ($issueGalleys as $issueGalley) {
            $this->updateSingleValueLocale($issueGalley->locale, 'issue_galleys', 'locale', 'galley_id', $issueGalley->galley_id);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new DowngradeNotSupportedException();
    }

    public function updateArrayLocaleNoId(string $dbLocales, string $table, string $column)
    {
        $siteSupportedLocales = json_decode($dbLocales);

        if ($siteSupportedLocales !== false) {
            $newLocales = [];
            foreach ($siteSupportedLocales as $siteSupportedLocale) {
                $newLocales[] = substr($siteSupportedLocale, 0, 2);
            }

            DB::table($table)
                ->update([
                    $column => $newLocales
                ]);
        }
    }

    public function updateArrayLocale(string $dbLocales, string $table, string $column, string $tableKeyColumn, int $id)
    {
        $siteSupportedLocales = json_decode($dbLocales);

        if ($siteSupportedLocales !== false) {
            $newLocales = [];
            foreach ($siteSupportedLocales as $siteSupportedLocale) {
                $newLocales[] = substr($siteSupportedLocale, 0, 2);
            }

            DB::table($table)
                ->where($tableKeyColumn, '=', $id)
                ->update([
                    $column => $newLocales
                ]);
        }
    }

    public function updateArrayLocaleSetting(string $dbLocales, string $table, string $settingValue, string $tableKeyColumn, int $id)
    {
        $siteSupportedLocales = json_decode($dbLocales);

        if ($siteSupportedLocales !== false) {
            $newLocales = [];
            foreach ($siteSupportedLocales as $siteSupportedLocale) {
                $newLocales[] = substr($siteSupportedLocale, 0, 2);
            }

            DB::table($table)
                ->where($tableKeyColumn, '=', $id)
                ->where('setting_name', '=', $settingValue)
                ->update([
                    'setting_value' => $newLocales
                ]);
        }
    }

    public function updateSingleValueLocale(string $localevalue, string $table, string $column, string $tableKeyColumn, int $id)
    {
        DB::table($table)
            ->where($tableKeyColumn, '=', $id)
            ->update([
                $column => substr($localevalue, 0, 2)
            ]);
    }

    public function updateSingleValueLocaleNoId(string $localevalue, string $table, string $column)
    {
        DB::table($table)
            ->update([
                $column => substr($localevalue, 0, 2)
            ]);
    }

    public function updateSingleValueLocaleEmailData(string $localevalue, string $table, string $column, string $tableKeyColumn, string $id)
    {
        DB::table($table)
            ->where($tableKeyColumn, '=', $id)
            ->where($column, '=', $localevalue)
            ->update([
                $column => substr($localevalue, 0, 2)
            ]);
    }
}
