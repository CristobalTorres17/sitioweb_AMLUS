<?php

namespace Database\Seeders\Legacy;

use App\Helpers\CharacterEncodingHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LegacyMenusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n🔗 MIGRATING MENUS...\n";

        $legacyMenus = DB::connection('legacy')
            ->table('menu')
            ->orderBy('id_menu')
            ->get();

        $count = 0;
        $errors = [];
        $menuMap = []; // Store mapping of legacy menu IDs to new menu IDs

        // First pass: Create all menus
        foreach ($legacyMenus as $legacy) {
            try {
                $title = CharacterEncodingHelper::convertCharset(trim($legacy->menu));
                $url = CharacterEncodingHelper::convertCharset(trim($legacy->ruta) ?: '#');

                // Determine if this is a link or parent menu
                // If it has submenu items, it becomes a parent
                $hasSubmenu = DB::connection('legacy')
                    ->table('submenu')
                    ->where('id_menu', $legacy->id_menu)
                    ->exists();

                $menu = DB::table('menus')->insertGetId([
                    'name' => $title,
                    'slug' => Str::slug($title) . '-' . uniqid(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Create menu item (top-level)
                if (!$hasSubmenu) {
                    DB::table('menu_items')->insert([
                        'menu_id' => $menu,
                        'parent_id' => null,
                        'title' => $title,
                        'url' => $url,
                        'target' => '_self',
                        'position' => 0,
                        'visibility' => json_encode([
                            'legacy_menu_id' => $legacy->id_menu,
                        ]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $menuMap[$legacy->id_menu] = $menu;
                $count++;
                echo "   ✓ Menu {$legacy->id_menu}: {$title}\n";
            } catch (\Exception $e) {
                $errors[] = "Menu {$legacy->id_menu}: " . $e->getMessage();
            }
        }

        // Second pass: Create menu items from submenus
        $legacySubmenus = DB::connection('legacy')
            ->table('submenu')
            ->orderBy('id_menu')
            ->orderBy('id_submenu')
            ->get();

        $itemCount = 0;
        foreach ($legacySubmenus as $legacy) {
            try {
                if (!isset($menuMap[$legacy->id_menu])) {
                    continue;
                }

                $label = CharacterEncodingHelper::convertCharset(trim($legacy->submenu));
                $url = CharacterEncodingHelper::convertCharset(trim($legacy->rutaSubmenu) ?: '#');
                $menuId = $menuMap[$legacy->id_menu];

                DB::table('menu_items')->insert([
                    'menu_id' => $menuId,
                    'parent_id' => null,
                    'title' => $label,
                    'url' => $url,
                    'target' => '_self',
                    'position' => $legacy->id_submenu,
                    'visibility' => json_encode([
                        'legacy_submenu_id' => $legacy->id_submenu,
                        'legacy_parent_id' => $legacy->id_menu,
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $itemCount++;
                echo "   ✓ Submenu {$legacy->id_submenu}: {$label} (Menu {$legacy->id_menu})\n";
            } catch (\Exception $e) {
                $errors[] = "Submenu {$legacy->id_submenu}: " . $e->getMessage();
            }
        }

        echo "\n📊 SUMMARY\n";
        echo "   Menus migrated: {$count}\n";
        echo "   Menu items (submenus) migrated: {$itemCount}\n";

        if (!empty($errors)) {
            echo "   Errors: " . count($errors) . "\n";
            foreach (array_slice($errors, 0, 5) as $error) {
                echo "      - {$error}\n";
            }
        }

        echo "\n";
    }
}
