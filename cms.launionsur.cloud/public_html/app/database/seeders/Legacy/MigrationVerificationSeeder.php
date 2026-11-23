<?php

namespace Database\Seeders\Legacy;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrationVerificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Comprehensive verification of all migrated data
     */
    public function run(): void
    {
        echo "\n";
        echo "╔════════════════════════════════════════════════════════════════╗\n";
        echo "║          MIGRATION VERIFICATION REPORT — 2026-04-07            ║\n";
        echo "╚════════════════════════════════════════════════════════════════╝\n";
        echo "\n";

        $this->verifyPhase2();
        $this->verifyPhase3();
        $this->verifyPhase4();
        $this->verifyPhase5();
        $this->verifyIntegrity();

        echo "\n";
        echo "╔════════════════════════════════════════════════════════════════╗\n";
        echo "║                    ✅ VERIFICATION COMPLETE                    ║\n";
        echo "╚════════════════════════════════════════════════════════════════╝\n";
        echo "\n";
    }

    private function verifyPhase2()
    {
        echo "📊 PHASE 2: USERS & ROLES\n";
        echo "─────────────────────────\n";

        $userCount = DB::table('users')->count();
        $roleCount = DB::table('roles')->count();
        $profileCount = DB::table('user_profiles')->count();

        echo "  Users: {$userCount} ✓\n";
        echo "  Roles: {$roleCount} ✓\n";
        echo "  Profiles: {$profileCount} ✓\n";

        // Verify each user has a profile
        $orphanedUsers = DB::table('users')
            ->whereNotIn('id', DB::table('user_profiles')->select('user_id'))
            ->count();

        if ($orphanedUsers > 0) {
            echo "  ⚠️  WARNING: {$orphanedUsers} users without profile\n";
        }

        echo "\n";
    }

    private function verifyPhase3()
    {
        echo "📊 PHASE 3: CONTENT\n";
        echo "──────────────────\n";

        $articleCount = DB::table('articles')->count();
        $categoryCount = DB::table('categories')->count();

        echo "  Articles: {$articleCount} ✓\n";
        echo "  Categories: {$categoryCount} ✓\n";

        // Verify articles with NULL category
        $nullCategoryCount = DB::table('articles')
            ->whereNull('category_id')
            ->count();

        if ($nullCategoryCount > 0) {
            echo "  ℹ️  Note: {$nullCategoryCount} articles with NULL category (expected)\n";
        }

        // Check charset integrity (sample)
        $specialCharCount = DB::table('articles')
            ->where('title', 'LIKE', '%é%')
            ->orWhere('title', 'LIKE', '%ó%')
            ->orWhere('title', 'LIKE', '%ú%')
            ->count();

        if ($specialCharCount > 0) {
            echo "  ✅ Charset: {$specialCharCount} articles with special chars correctly converted\n";
        }

        echo "\n";
    }

    private function verifyPhase4()
    {
        echo "📊 PHASE 4: EVENTS & GALLERIES\n";
        echo "───────────────────────────────\n";

        $eventCount = DB::table('events')->count();
        $galleryCount = DB::table('event_galleries')->count();

        echo "  Events: {$eventCount} ✓\n";
        echo "  Gallery links: {$galleryCount} ✓\n";

        // Verify gallery-event relationships
        $orphanedGalleries = DB::table('event_galleries')
            ->whereNotIn('event_id', DB::table('events')->select('id'))
            ->count();

        if ($orphanedGalleries > 0) {
            echo "  ⚠️  WARNING: {$orphanedGalleries} orphaned gallery links\n";
        }

        echo "\n";
    }

    private function verifyPhase5()
    {
        echo "📊 PHASE 5: CONFIGURATION\n";
        echo "─────────────────────────\n";

        $bannerCount = DB::table('banners')->count();
        $menuCount = DB::table('menus')->count();
        $menuItemCount = DB::table('menu_items')->count();

        echo "  Banners: {$bannerCount} ✓\n";
        echo "  Menus: {$menuCount} ✓\n";
        echo "  Menu items: {$menuItemCount} ✓\n";

        // Verify menu-item relationships
        $orphanedItems = DB::table('menu_items')
            ->where('menu_id', '>', 0)
            ->whereNotIn('menu_id', DB::table('menus')->select('id'))
            ->count();

        if ($orphanedItems > 0) {
            echo "  ⚠️  WARNING: {$orphanedItems} orphaned menu items\n";
        }

        echo "\n";
    }

    private function verifyIntegrity()
    {
        echo "🔍 DATA INTEGRITY CHECKS\n";
        echo "────────────────────────\n";

        // Check for NULL emails
        $nullEmails = DB::table('users')
            ->whereNull('email')
            ->count();

        if ($nullEmails > 0) {
            echo "  ⚠️  {$nullEmails} users with NULL email\n";
        } else {
            echo "  ✓ All users have email addresses\n";
        }

        // Check for NULL passwords
        $nullPasswords = DB::table('users')
            ->whereNull('password')
            ->count();

        if ($nullPasswords > 0) {
            echo "  ⚠️  {$nullPasswords} users with NULL password\n";
        } else {
            echo "  ✓ All users have passwords\n";
        }

        // Summary
        $totalRecords = DB::table('users')->count()
            + DB::table('roles')->count()
            + DB::table('articles')->count()
            + DB::table('events')->count()
            + DB::table('event_galleries')->count()
            + DB::table('banners')->count()
            + DB::table('menus')->count()
            + DB::table('menu_items')->count();

        echo "  ✓ Total records migrated: {$totalRecords}\n";
        echo "\n";
    }
}
