<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class EcommerceSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'slug' => 'industrial-chemicals',
                'name_en' => 'Industrial Chemicals',
                'name_ar' => 'الكيماويات الصناعية',
                'description_en' => 'Reliable chemical products for manufacturing and industrial processing.',
                'description_ar' => 'منتجات كيميائية موثوقة للتصنيع والعمليات الصناعية.',
                'sort_order' => 10,
            ],
            [
                'slug' => 'laboratory-chemicals',
                'name_en' => 'Laboratory Chemicals',
                'name_ar' => 'كيماويات المعامل',
                'description_en' => 'Analytical and laboratory-grade chemicals for research and quality control.',
                'description_ar' => 'كيماويات تحليلية ومعملية للأبحاث ومراقبة الجودة.',
                'sort_order' => 20,
            ],
            [
                'slug' => 'acids',
                'parent_slug' => 'industrial-chemicals',
                'name_en' => 'Acids',
                'name_ar' => 'الأحماض',
                'description_en' => 'Industrial and laboratory acids supplied in controlled concentrations.',
                'description_ar' => 'أحماض صناعية ومعملية بتركيزات محكومة.',
                'sort_order' => 30,
            ],
            [
                'slug' => 'alkalis',
                'parent_slug' => 'industrial-chemicals',
                'name_en' => 'Alkalis',
                'name_ar' => 'القلويات',
                'description_en' => 'Alkaline products for treatment, cleaning, and industrial processing.',
                'description_ar' => 'منتجات قلوية للمعالجة والتنظيف والعمليات الصناعية.',
                'sort_order' => 40,
            ],
            [
                'slug' => 'solvents',
                'parent_slug' => 'laboratory-chemicals',
                'name_en' => 'Solvents',
                'name_ar' => 'المذيبات',
                'description_en' => 'General-purpose and laboratory solvents with documented specifications.',
                'description_ar' => 'مذيبات عامة ومعملية بمواصفات موثقة.',
                'sort_order' => 50,
            ],
        ];

        $categoryModels = [];

        foreach ($categories as $categoryData) {
            $parentSlug = $categoryData['parent_slug'] ?? null;
            unset($categoryData['parent_slug']);

            $category = Category::withTrashed()->updateOrCreate(
                ['slug' => $categoryData['slug']],
                [
                    ...$categoryData,
                    'parent_id' => $parentSlug === null ? null : $categoryModels[$parentSlug]->id,
                    'is_active' => true,
                ],
            );
            $category->restore();
            $categoryModels[$category->slug] = $category;
        }

        $products = [
            [
                'category' => 'acids',
                'sku' => 'ACTA-ACID-HCL-32',
                'slug' => 'hydrochloric-acid-32',
                'name_en' => 'Hydrochloric Acid 32%',
                'name_ar' => 'حمض الهيدروكلوريك 32٪',
                'short_description_en' => 'Industrial-grade hydrochloric acid for controlled processing applications.',
                'short_description_ar' => 'حمض هيدروكلوريك صناعي لتطبيقات المعالجة المحكومة.',
                'description_en' => 'A consistent 32% hydrochloric acid solution supplied for industrial cleaning, pH control, and chemical processing.',
                'description_ar' => 'محلول حمض هيدروكلوريك بتركيز 32٪ للتنظيف الصناعي وضبط الأس الهيدروجيني والمعالجة الكيميائية.',
                'price' => 850.00,
                'sale_price' => 799.00,
                'stock_quantity' => 80,
                'is_featured' => true,
            ],
            [
                'category' => 'acids',
                'sku' => 'ACTA-ACID-H2SO4-98',
                'slug' => 'sulfuric-acid-98',
                'name_en' => 'Sulfuric Acid 98%',
                'name_ar' => 'حمض الكبريتيك 98٪',
                'short_description_en' => 'High-concentration sulfuric acid for qualified industrial users.',
                'short_description_ar' => 'حمض كبريتيك عالي التركيز للمستخدمين الصناعيين المؤهلين.',
                'description_en' => 'High-purity sulfuric acid suitable for manufacturing, treatment, and laboratory preparation under approved safety procedures.',
                'description_ar' => 'حمض كبريتيك عالي النقاء مناسب للتصنيع والمعالجة والتحضير المعملي وفق إجراءات السلامة المعتمدة.',
                'price' => 1250.00,
                'sale_price' => null,
                'stock_quantity' => 45,
                'is_featured' => true,
            ],
            [
                'category' => 'acids',
                'sku' => 'ACTA-ACID-HNO3-68',
                'slug' => 'nitric-acid-68',
                'name_en' => 'Nitric Acid 68%',
                'name_ar' => 'حمض النيتريك 68٪',
                'short_description_en' => 'Concentrated nitric acid with documented quality specifications.',
                'short_description_ar' => 'حمض نيتريك مركز بمواصفات جودة موثقة.',
                'description_en' => 'A controlled nitric acid solution intended for professional industrial and analytical applications.',
                'description_ar' => 'محلول حمض نيتريك محكوم للاستخدامات الصناعية والتحليلية المتخصصة.',
                'price' => 1450.00,
                'sale_price' => null,
                'stock_quantity' => 30,
            ],
            [
                'category' => 'alkalis',
                'sku' => 'ACTA-ALK-NAOH-FLAKE',
                'slug' => 'sodium-hydroxide-flakes',
                'name_en' => 'Sodium Hydroxide Flakes',
                'name_ar' => 'رقائق هيدروكسيد الصوديوم',
                'short_description_en' => 'Industrial caustic soda flakes for processing and cleaning.',
                'short_description_ar' => 'رقائق صودا كاوية صناعية للمعالجة والتنظيف.',
                'description_en' => 'Industrial sodium hydroxide flakes supplied in sealed packaging for qualified handling environments.',
                'description_ar' => 'رقائق هيدروكسيد الصوديوم الصناعية في عبوات محكمة لبيئات التداول المؤهلة.',
                'price' => 975.00,
                'sale_price' => 925.00,
                'stock_quantity' => 120,
                'is_featured' => true,
            ],
            [
                'category' => 'alkalis',
                'sku' => 'ACTA-ALK-KOH-90',
                'slug' => 'potassium-hydroxide-90',
                'name_en' => 'Potassium Hydroxide 90%',
                'name_ar' => 'هيدروكسيد البوتاسيوم 90٪',
                'short_description_en' => 'Industrial potassium hydroxide for chemical manufacturing.',
                'short_description_ar' => 'هيدروكسيد بوتاسيوم صناعي للتصنيع الكيميائي.',
                'description_en' => 'Potassium hydroxide supplied for professional manufacturing and formulation applications.',
                'description_ar' => 'هيدروكسيد البوتاسيوم لتطبيقات التصنيع والتركيب المتخصصة.',
                'price' => 1650.00,
                'sale_price' => null,
                'stock_quantity' => 35,
            ],
            [
                'category' => 'solvents',
                'sku' => 'ACTA-SOLV-ACETONE-AR',
                'slug' => 'acetone-analytical-grade',
                'name_en' => 'Acetone – Analytical Grade',
                'name_ar' => 'أسيتون درجة تحليلية',
                'short_description_en' => 'Analytical-grade acetone for laboratory cleaning and preparation.',
                'short_description_ar' => 'أسيتون بدرجة تحليلية للتنظيف والتحضير المعملي.',
                'description_en' => 'Documented analytical-grade acetone for controlled laboratory workflows and sample preparation.',
                'description_ar' => 'أسيتون بدرجة تحليلية موثقة لإجراءات المعامل المحكومة وتحضير العينات.',
                'price' => 620.00,
                'sale_price' => null,
                'stock_quantity' => 75,
                'is_featured' => true,
            ],
            [
                'category' => 'solvents',
                'sku' => 'ACTA-SOLV-ETH-99',
                'slug' => 'ethanol-99',
                'name_en' => 'Ethanol 99%',
                'name_ar' => 'إيثانول 99٪',
                'short_description_en' => 'High-purity ethanol for laboratory and industrial formulation.',
                'short_description_ar' => 'إيثانول عالي النقاء للمعامل والتركيبات الصناعية.',
                'description_en' => 'High-purity ethanol supplied with controlled specifications for professional use.',
                'description_ar' => 'إيثانول عالي النقاء بمواصفات محكومة للاستخدام المتخصص.',
                'price' => 740.00,
                'sale_price' => 699.00,
                'stock_quantity' => 90,
            ],
            [
                'category' => 'solvents',
                'sku' => 'ACTA-SOLV-IPA-99',
                'slug' => 'isopropyl-alcohol-99',
                'name_en' => 'Isopropyl Alcohol 99%',
                'name_ar' => 'كحول أيزوبروبيلي 99٪',
                'short_description_en' => 'High-purity IPA for professional cleaning and processing.',
                'short_description_ar' => 'كحول أيزوبروبيلي عالي النقاء للتنظيف والمعالجة المتخصصة.',
                'description_en' => 'A high-purity isopropyl alcohol product for controlled cleaning and industrial applications.',
                'description_ar' => 'منتج كحول أيزوبروبيلي عالي النقاء لتطبيقات التنظيف والصناعة المحكومة.',
                'price' => 690.00,
                'sale_price' => null,
                'stock_quantity' => 65,
            ],
            [
                'category' => 'laboratory-chemicals',
                'sku' => 'ACTA-LAB-NACL-AR',
                'slug' => 'sodium-chloride-analytical-grade',
                'name_en' => 'Sodium Chloride – Analytical Grade',
                'name_ar' => 'كلوريد الصوديوم درجة تحليلية',
                'short_description_en' => 'Analytical reagent for laboratory preparation and quality control.',
                'short_description_ar' => 'كاشف تحليلي للتحضير المعملي ومراقبة الجودة.',
                'description_en' => 'Analytical-grade sodium chloride for solution preparation, calibration, and controlled laboratory procedures.',
                'description_ar' => 'كلوريد صوديوم بدرجة تحليلية لتحضير المحاليل والمعايرة والإجراءات المعملية المحكومة.',
                'price' => 280.00,
                'sale_price' => null,
                'stock_quantity' => 150,
            ],
            [
                'category' => 'laboratory-chemicals',
                'sku' => 'ACTA-LAB-PH-BUFFER',
                'slug' => 'ph-buffer-solution-set',
                'name_en' => 'pH Buffer Solution Set',
                'name_ar' => 'مجموعة محاليل منظمة للأس الهيدروجيني',
                'short_description_en' => 'Buffer set for routine pH meter calibration.',
                'short_description_ar' => 'مجموعة محاليل لمعايرة أجهزة قياس الأس الهيدروجيني.',
                'description_en' => 'A laboratory buffer set designed for routine calibration and quality-control workflows.',
                'description_ar' => 'مجموعة محاليل معملية للمعايرة الدورية وإجراءات مراقبة الجودة.',
                'price' => 450.00,
                'sale_price' => 399.00,
                'stock_quantity' => 60,
                'is_featured' => true,
            ],
        ];

        foreach ($products as $index => $productData) {
            $categorySlug = $productData['category'];
            unset($productData['category']);

            $product = Product::withTrashed()->updateOrCreate(
                ['sku' => $productData['sku']],
                [
                    ...$productData,
                    'category_id' => $categoryModels[$categorySlug]->id,
                    'track_inventory' => true,
                    'is_active' => true,
                    'is_featured' => $productData['is_featured'] ?? false,
                    'sort_order' => ($index + 1) * 10,
                ],
            );
            $product->restore();
        }
    }
}
