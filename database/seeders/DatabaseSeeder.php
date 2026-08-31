<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Contact;
use App\Models\Event;
use App\Models\Setting;
use App\Models\SiteContent;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    protected bool $siteContentOnly = false;

    public function seedSiteContentsOnly(): void
    {
        $this->siteContentOnly = true;

        $this->run();
    }

    public function run(): void
    {
        if (! $this->siteContentOnly) {
            User::updateOrCreate([
                'email' => 'admin@gmail.com',
            ], [
                'name' => 'Acta-Chemical Panel',
                'password' => Hash::make('password'),
            ]);

            Setting::updateOrCreate([
                'title_en' => 'Acta Chemical',
            ], [
                'title_en' => 'Acta Chemical',
                'title_ar' => 'اكتا كيميكال',
                'description_en' => 'Acta Chemical supplies high-quality industrial and laboratory chemicals with reliable documentation, safe handling, and responsive support.',
                'description_ar' => 'توفر اكتا كيميكال كيماويات صناعية ومعملية عالية الجودة مع توثيق موثوق، تداول آمن، ودعم سريع للعملاء.',
                'watts' => '+201001234567',
                'email' => 'info@actachemical.com',
                'phone' => '+20212345678',
                'lat' => '30.0444',
                'lng' => '31.2357',
                'address' => 'Cairo, Egypt',
            ]);

            $this->call(SiteContentSeeder::class);
            $this->call(EcommerceSeeder::class);
            $this->call(AdminAuthorizationSeeder::class);

            if (app()->environment(['local', 'testing'])) {
                $this->call(CommerceDemoSeeder::class);
            }
        }

        if ($this->siteContentOnly) {
            $siteContents = [
                [
                    'key' => 'hero_badge',
                    'group' => 'Homepage / Hero',
                    'label' => 'Hero badge',
                    'value_en' => 'Shaping the Future of Chemistry',
                    'value_ar' => 'نصنع مستقبل الكيمياء',
                ],
                [
                    'key' => 'hero_explore_articles',
                    'group' => 'Homepage / Hero',
                    'label' => 'Hero articles call to action',
                    'value_en' => 'Explore Our Articles',
                    'value_ar' => 'استكشف مقالاتنا',
                ],
                [
                    'key' => 'hero_upcoming_events',
                    'group' => 'Homepage / Hero',
                    'label' => 'Hero events call to action',
                    'value_en' => 'Upcoming Events',
                    'value_ar' => 'الفعاليات القادمة',
                ],
                [
                    'key' => 'hero_highlight_1',
                    'group' => 'Homepage / Hero',
                    'label' => 'Hero highlight 1',
                    'value_en' => 'Precision Chemical Synthesis & Industrial Solutions',
                    'value_ar' => 'التخليق الكيميائي الدقيق والحلول الصناعية',
                ],
                [
                    'key' => 'hero_highlight_2',
                    'group' => 'Homepage / Hero',
                    'label' => 'Hero highlight 2',
                    'value_en' => 'Sustainable & High-Performance Molecular Catalysts',
                    'value_ar' => 'محفزات جزيئية مستدامة وعالية الأداء',
                ],
                [
                    'key' => 'hero_highlight_3',
                    'group' => 'Homepage / Hero',
                    'label' => 'Hero highlight 3',
                    'value_en' => 'Global Quality Standards & ISO 9001 Compliance',
                    'value_ar' => 'معايير جودة عالمية والالتزام بمعيار ISO 9001',
                ],
                [
                    'key' => 'hero_purity_label',
                    'group' => 'Homepage / Hero',
                    'label' => 'Hero purity badge label',
                    'value_en' => 'Chemical Purity',
                    'value_ar' => 'النقاء الكيميائي',
                ],
                [
                    'key' => 'hero_purity_value',
                    'group' => 'Homepage / Hero',
                    'label' => 'Hero purity badge value',
                    'value_en' => '99.8% Certified Grade',
                    'value_ar' => 'درجة نقاء معتمدة 99.8٪',
                ],
                [
                    'key' => 'hero_standard_label',
                    'group' => 'Homepage / Hero',
                    'label' => 'Hero standards badge label',
                    'value_en' => 'Global Standards',
                    'value_ar' => 'المعايير العالمية',
                ],
                [
                    'key' => 'hero_standard_value',
                    'group' => 'Homepage / Hero',
                    'label' => 'Hero standards badge value',
                    'value_en' => 'ISO 9001 Compliant',
                    'value_ar' => 'متوافق مع معيار ISO 9001',
                ],
                [
                    'key' => 'hero_image',
                    'group' => 'Homepage / Hero',
                    'label' => 'Hero image description',
                    'value_en' => 'Scientist conducting chemical research in an ACTA laboratory',
                    'value_ar' => 'باحثة تجري أبحاثًا كيميائية في مختبرات أكتا',
                ],
                [
                    'key' => 'about_badge',
                    'group' => 'Homepage / About Us',
                    'label' => 'About Us badge',
                    'value_en' => 'Who We Are',
                    'value_ar' => 'من نحن',
                ],
                [
                    'key' => 'about_title',
                    'group' => 'Homepage / About Us',
                    'label' => 'About Us title',
                    'value_en' => 'About ACTA Chemical',
                    'value_ar' => 'عن أكتا للكيماويات',
                ],
                [
                    'key' => 'about_description',
                    'group' => 'Homepage / About Us',
                    'label' => 'About Us description',
                    'value_en' => 'ACTA Chemical delivers dependable industrial and laboratory chemical solutions, combining verified quality, safe handling, and responsive technical support.',
                    'value_ar' => 'تقدم أكتا للكيماويات حلولًا كيميائية صناعية ومعملية موثوقة، تجمع بين الجودة الموثقة والتداول الآمن والدعم الفني السريع.',
                ],
                [
                    'key' => 'about_image',
                    'group' => 'Homepage / About Us',
                    'label' => 'About Us image description',
                    'value_en' => 'ACTA Chemical laboratories and industrial operations',
                    'value_ar' => 'مختبرات وعمليات أكتا للكيماويات',
                ],
                [
                    'key' => 'mission_title',
                    'group' => 'Homepage / About Us',
                    'label' => 'Mission title',
                    'value_en' => 'Our Mission',
                    'value_ar' => 'رسالتنا',
                ],
                [
                    'key' => 'mission_description',
                    'group' => 'Homepage / About Us',
                    'label' => 'Mission description',
                    'value_en' => 'To provide precise, safe, and dependable chemical solutions that help our partners operate with confidence and efficiency.',
                    'value_ar' => 'تقديم حلول كيميائية دقيقة وآمنة وموثوقة تساعد شركاءنا على العمل بثقة وكفاءة.',
                ],
                [
                    'key' => 'vision_title',
                    'group' => 'Homepage / About Us',
                    'label' => 'Vision title',
                    'value_en' => 'Our Vision',
                    'value_ar' => 'رؤيتنا',
                ],
                [
                    'key' => 'vision_description',
                    'group' => 'Homepage / About Us',
                    'label' => 'Vision description',
                    'value_en' => 'To be a trusted regional leader in high-quality chemical solutions, driven by science, sustainability, and customer success.',
                    'value_ar' => 'أن نكون شريكًا إقليميًا موثوقًا ورائدًا في الحلول الكيميائية عالية الجودة، مدفوعين بالعلم والاستدامة ونجاح عملائنا.',
                ],
                [
                    'key' => 'stat_1_value',
                    'group' => 'Homepage / Statistics',
                    'label' => 'Statistic 1 value',
                    'value_en' => '15+',
                    'value_ar' => '15+',
                ],
                [
                    'key' => 'stat_1_label',
                    'group' => 'Homepage / Statistics',
                    'label' => 'Statistic 1 label',
                    'value_en' => 'Years of Industrial Excellence',
                    'value_ar' => 'عامًا من التميز الصناعي',
                ],
                [
                    'key' => 'stat_1_icon',
                    'group' => 'Homepage / Statistics',
                    'label' => 'Statistic 1 icon key',
                    'value_en' => 'award',
                    'value_ar' => 'award',
                ],
                [
                    'key' => 'stat_2_value',
                    'group' => 'Homepage / Statistics',
                    'label' => 'Statistic 2 value',
                    'value_en' => '250+',
                    'value_ar' => '250+',
                ],
                [
                    'key' => 'stat_2_label',
                    'group' => 'Homepage / Statistics',
                    'label' => 'Statistic 2 label',
                    'value_en' => 'Precision Chemical Solutions',
                    'value_ar' => 'حلول كيميائية دقيقة',
                ],
                [
                    'key' => 'stat_2_icon',
                    'group' => 'Homepage / Statistics',
                    'label' => 'Statistic 2 icon key',
                    'value_en' => 'flask-conical',
                    'value_ar' => 'flask-conical',
                ],
                [
                    'key' => 'stat_3_value',
                    'group' => 'Homepage / Statistics',
                    'label' => 'Statistic 3 value',
                    'value_en' => '40+',
                    'value_ar' => '40+',
                ],
                [
                    'key' => 'stat_3_label',
                    'group' => 'Homepage / Statistics',
                    'label' => 'Statistic 3 label',
                    'value_en' => 'Global Enterprise Partners',
                    'value_ar' => 'شركاء من المؤسسات العالمية',
                ],
                [
                    'key' => 'stat_3_icon',
                    'group' => 'Homepage / Statistics',
                    'label' => 'Statistic 3 icon key',
                    'value_en' => 'globe-2',
                    'value_ar' => 'globe-2',
                ],
                [
                    'key' => 'articles_title',
                    'group' => 'Articles Page',
                    'label' => 'Articles page title',
                    'value_en' => 'Scientific Insights',
                    'value_ar' => 'رؤى علمية',
                ],
                [
                    'key' => 'articles_description',
                    'group' => 'Articles Page',
                    'label' => 'Articles page description',
                    'value_en' => 'Explore our latest research, industrial breakthroughs, and comprehensive guides on chemical innovation and safety.',
                    'value_ar' => 'استكشف أحدث أبحاثنا وإنجازاتنا الصناعية وأدلّتنا الشاملة حول الابتكار والسلامة الكيميائية.',
                ],
                [
                    'key' => 'articles_view_all',
                    'group' => 'Homepage / Articles',
                    'label' => 'Articles view all button',
                    'value_en' => 'View All Articles',
                    'value_ar' => 'مشاهدة جميع المقالات',
                ],
                [
                    'key' => 'articles_read_more',
                    'group' => 'Homepage / Articles',
                    'label' => 'Articles read more label',
                    'value_en' => 'Read Full Article',
                    'value_ar' => 'اقرأ المقال كاملًا',
                ],
                [
                    'key' => 'articles_empty_title',
                    'group' => 'Articles Page',
                    'label' => 'Articles empty-state title',
                    'value_en' => 'Library is empty',
                    'value_ar' => 'المكتبة فارغة',
                ],
                [
                    'key' => 'articles_empty_description',
                    'group' => 'Articles Page',
                    'label' => 'Articles empty-state description',
                    'value_en' => 'Our research library is currently being updated. Please check back later for the latest scientific insights.',
                    'value_ar' => 'نعمل حاليًا على تحديث مكتبة الأبحاث. يُرجى العودة لاحقًا للاطلاع على أحدث الرؤى العلمية.',
                ],
                [
                    'key' => 'articles_badge',
                    'group' => 'Homepage / Articles',
                    'label' => 'Articles section badge',
                    'value_en' => 'Scientific Insights',
                    'value_ar' => 'رؤى علمية',
                ],
                [
                    'key' => 'events_title',
                    'group' => 'Events Page',
                    'label' => 'Events page title',
                    'value_en' => 'Scientific Summits & Industrial Workshops',
                    'value_ar' => 'قمم علمية وورش عمل صناعية',
                ],
                [
                    'key' => 'events_description',
                    'group' => 'Events Page',
                    'label' => 'Events page description',
                    'value_en' => 'Discover the world of advanced chemistry through our specialized summits and workshops. Engage with us to expand your professional horizons.',
                    'value_ar' => 'اكتشف عالم الكيمياء المتقدمة من خلال قممنا وورش العمل المتخصصة. تواصل معنا لتوسيع آفاقك المهنية.',
                ],
                [
                    'key' => 'events_view_all',
                    'group' => 'Homepage / Events',
                    'label' => 'Events view all button',
                    'value_en' => 'View All Events',
                    'value_ar' => 'مشاهدة جميع الفعاليات',
                ],
                [
                    'key' => 'events_discover_more',
                    'group' => 'Homepage / Events',
                    'label' => 'Events discover more label',
                    'value_en' => 'Discover More',
                    'value_ar' => 'اكتشف المزيد',
                ],
                [
                    'key' => 'events_empty_title',
                    'group' => 'Events Page',
                    'label' => 'Events empty-state title',
                    'value_en' => 'No upcoming events',
                    'value_ar' => 'لا توجد فعاليات قادمة',
                ],
                [
                    'key' => 'events_empty_description',
                    'group' => 'Events Page',
                    'label' => 'Events empty-state description',
                    'value_en' => 'No upcoming events are scheduled at the moment. Please check back later for our latest summits and workshops.',
                    'value_ar' => 'لا توجد فعاليات مجدولة حاليًا. يُرجى العودة لاحقًا للاطلاع على أحدث قممنا وورش العمل لدينا.',
                ],
                [
                    'key' => 'event_detail_category',
                    'group' => 'Event Detail Page',
                    'label' => 'Event detail category label',
                    'value_en' => 'Industrial Event',
                    'value_ar' => 'فعالية صناعية',
                ],
                [
                    'key' => 'events_badge',
                    'group' => 'Homepage / Events',
                    'label' => 'Events section badge',
                    'value_en' => 'Summits & Workshops',
                    'value_ar' => 'القمم وورش العمل',
                ],
                [
                    'key' => 'events_card_badge',
                    'group' => 'Homepage / Events',
                    'label' => 'Event card badge',
                    'value_en' => 'Industrial Event',
                    'value_ar' => 'فعالية صناعية',
                ],
                [
                    'key' => 'events_card_location',
                    'group' => 'Homepage / Events',
                    'label' => 'Event card location label',
                    'value_en' => 'Location',
                    'value_ar' => 'الموقع',
                ],
                [
                    'key' => 'article_detail_cta_description',
                    'group' => 'Article Detail Page',
                    'label' => 'Article detail call to action description',
                    'value_en' => 'Additional technical details and research data are available for registered industrial partners. Contact our research team to learn more.',
                    'value_ar' => 'تتوفر تفاصيل فنية إضافية وبيانات بحثية للشركاء الصناعيين المسجلين. تواصل مع فريق الأبحاث لدينا لمعرفة المزيد.',
                ],
                [
                    'key' => 'footer_quality_badge',
                    'group' => 'Global / Footer',
                    'label' => 'Footer quality badge',
                    'value_en' => 'ISO Certified Quality',
                    'value_ar' => 'جودة معتمدة وفق ISO',
                ],
                [
                    'key' => 'footer_phone_label',
                    'group' => 'Global / Footer',
                    'label' => 'Footer phone label',
                    'value_en' => 'Direct Line',
                    'value_ar' => 'خط الاتصال المباشر',
                ],
                [
                    'key' => 'footer_email_label',
                    'group' => 'Global / Footer',
                    'label' => 'Footer email label',
                    'value_en' => 'Email Inquiry',
                    'value_ar' => 'للاستفسار عبر البريد الإلكتروني',
                ],
                [
                    'key' => 'footer_location_title',
                    'group' => 'Global / Footer',
                    'label' => 'Footer location title',
                    'value_en' => 'Headquarters',
                    'value_ar' => 'المقر الرئيسي',
                ],
                [
                    'key' => 'contact_badge',
                    'group' => 'Contact Page',
                    'label' => 'Contact page badge',
                    'value_en' => 'Scientific Consultation & Support',
                    'value_ar' => 'استشارات ودعم علمي',
                ],
                [
                    'key' => 'contact_title',
                    'group' => 'Contact Page',
                    'label' => 'Contact page title',
                    'value_en' => 'Get in Touch',
                    'value_ar' => 'تواصل معنا',
                ],
                [
                    'key' => 'contact_description',
                    'group' => 'Contact Page',
                    'label' => 'Contact page description',
                    'value_en' => 'Have a scientific inquiry or need custom industrial chemical formulations? Our team of experts is here to assist you.',
                    'value_ar' => 'هل لديك استفسار علمي أو تحتاج إلى تركيبات كيميائية صناعية مخصصة؟ فريق خبرائنا جاهز لمساعدتك.',
                ],
                [
                    'key' => 'contact_phone_title',
                    'group' => 'Contact Page',
                    'label' => 'Contact phone title',
                    'value_en' => 'Call Direct',
                    'value_ar' => 'اتصل مباشرة',
                ],
                [
                    'key' => 'contact_phone_description',
                    'group' => 'Contact Page',
                    'label' => 'Contact phone description',
                    'value_en' => 'Sunday–Thursday, 9:00 AM–5:00 PM',
                    'value_ar' => 'من الأحد إلى الخميس، من 9:00 صباحًا إلى 5:00 مساءً',
                ],
                [
                    'key' => 'contact_email_title',
                    'group' => 'Contact Page',
                    'label' => 'Contact email title',
                    'value_en' => 'Email Support',
                    'value_ar' => 'الدعم عبر البريد الإلكتروني',
                ],
                [
                    'key' => 'contact_email_description',
                    'group' => 'Contact Page',
                    'label' => 'Contact email description',
                    'value_en' => 'We respond to email inquiries within 24 hours.',
                    'value_ar' => 'نرد على الاستفسارات عبر البريد الإلكتروني خلال 24 ساعة.',
                ],
                [
                    'key' => 'contact_location_title',
                    'group' => 'Contact Page',
                    'label' => 'Contact location title',
                    'value_en' => 'Our Location',
                    'value_ar' => 'موقعنا',
                ],
                [
                    'key' => 'contact_location_description',
                    'group' => 'Contact Page',
                    'label' => 'Contact location description',
                    'value_en' => 'Visit our headquarters or contact us for directions.',
                    'value_ar' => 'تفضل بزيارة مقرنا أو تواصل معنا للحصول على الاتجاهات.',
                ],
                [
                    'key' => 'contact_form_title',
                    'group' => 'Contact Page',
                    'label' => 'Contact form title',
                    'value_en' => 'Send Us a Direct Inquiry',
                    'value_ar' => 'أرسل لنا استفسارك مباشرة',
                ],
                [
                    'key' => 'contact_form_description',
                    'group' => 'Contact Page',
                    'label' => 'Contact form description',
                    'value_en' => 'Complete the form below and our team will contact you.',
                    'value_ar' => 'املأ النموذج أدناه وسيتواصل معك فريقنا.',
                ],
                [
                    'key' => 'privacy_back',
                    'group' => 'Privacy Page',
                    'label' => 'Privacy back link',
                    'value_en' => 'Back to Home',
                    'value_ar' => 'العودة إلى الرئيسية',
                ],
                [
                    'key' => 'privacy_title',
                    'group' => 'Privacy Page',
                    'label' => 'Privacy page title',
                    'value_en' => 'Privacy Policy',
                    'value_ar' => 'سياسة الخصوصية',
                ],
                [
                    'key' => 'privacy_subtitle',
                    'group' => 'Privacy Page',
                    'label' => 'Privacy page subtitle',
                    'value_en' => 'ACTA Chemical Data Compliance & Safety Protocol Standards',
                    'value_ar' => 'معايير أكتا للكيماويات لامتثال البيانات وبروتوكولات السلامة',
                ],
                [
                    'key' => 'privacy_intro',
                    'group' => 'Privacy Page',
                    'label' => 'Privacy page introduction',
                    'value_en' => 'ACTA Chemical is committed to protecting your privacy and ensuring the confidentiality of your industrial data and scientific partnerships.',
                    'value_ar' => 'تلتزم أكتا للكيماويات بحماية خصوصيتك وضمان سرية بياناتك الصناعية وشراكاتك العلمية.',
                ],
                [
                    'key' => 'privacy_section_1_title',
                    'group' => 'Privacy Page',
                    'label' => 'Privacy section 1 title',
                    'value_en' => 'Data Collection',
                    'value_ar' => 'جمع البيانات',
                ],
                [
                    'key' => 'privacy_section_1_content',
                    'group' => 'Privacy Page',
                    'label' => 'Privacy section 1 content',
                    'value_en' => 'We collect the information needed to provide our chemical services and improve your experience on our platform.',
                    'value_ar' => 'نجمع المعلومات اللازمة لتقديم خدماتنا الكيميائية وتحسين تجربتك على منصتنا.',
                ],
                [
                    'key' => 'privacy_section_2_title',
                    'group' => 'Privacy Page',
                    'label' => 'Privacy section 2 title',
                    'value_en' => 'Information Security',
                    'value_ar' => 'أمن المعلومات',
                ],
                [
                    'key' => 'privacy_section_2_content',
                    'group' => 'Privacy Page',
                    'label' => 'Privacy section 2 content',
                    'value_en' => 'ACTA Chemical applies industry-standard security measures to protect your industrial data from unauthorized access.',
                    'value_ar' => 'تطبق أكتا للكيماويات إجراءات أمنية وفق معايير القطاع لحماية بياناتك الصناعية من الوصول غير المصرح به.',
                ],
                [
                    'key' => 'privacy_section_3_title',
                    'group' => 'Privacy Page',
                    'label' => 'Privacy section 3 title',
                    'value_en' => 'Your Rights',
                    'value_ar' => 'حقوقك',
                ],
                [
                    'key' => 'privacy_section_3_content',
                    'group' => 'Privacy Page',
                    'label' => 'Privacy section 3 content',
                    'value_en' => 'You have the right to access, correct, or request deletion of your personal and corporate information stored in our systems.',
                    'value_ar' => 'يحق لك الوصول إلى معلوماتك الشخصية ومعلومات مؤسستك المخزنة في أنظمتنا وتصحيحها أو طلب حذفها.',
                ],
                [
                    'key' => 'privacy_callout_title',
                    'group' => 'Privacy Page',
                    'label' => 'Privacy callout title',
                    'value_en' => 'Contact Compliance Team',
                    'value_ar' => 'تواصل مع فريق الامتثال',
                ],
                [
                    'key' => 'privacy_callout_description',
                    'group' => 'Privacy Page',
                    'label' => 'Privacy callout description',
                    'value_en' => 'If you have questions about how we handle your industrial chemical data, please contact our compliance department.',
                    'value_ar' => 'إذا كانت لديك أسئلة حول كيفية تعاملنا مع بياناتك المتعلقة بالمواد الكيميائية الصناعية، فيُرجى التواصل مع قسم الامتثال.',
                ],
                [
                    'key' => 'privacy_callout_button',
                    'group' => 'Privacy Page',
                    'label' => 'Privacy callout button',
                    'value_en' => 'Contact Compliance',
                    'value_ar' => 'تواصل مع فريق الامتثال',
                ],
                [
                    'key' => 'terms_back',
                    'group' => 'Terms Page',
                    'label' => 'Terms back link',
                    'value_en' => 'Back to Home',
                    'value_ar' => 'العودة إلى الرئيسية',
                ],
                [
                    'key' => 'terms_title',
                    'group' => 'Terms Page',
                    'label' => 'Terms page title',
                    'value_en' => 'Terms of Service',
                    'value_ar' => 'شروط الاستخدام',
                ],
                [
                    'key' => 'terms_subtitle',
                    'group' => 'Terms Page',
                    'label' => 'Terms page subtitle',
                    'value_en' => 'Industrial Terms, Quality Guidelines & Scientific Usage Standards',
                    'value_ar' => 'الشروط الصناعية وإرشادات الجودة ومعايير الاستخدام العلمي',
                ],
                [
                    'key' => 'terms_intro',
                    'group' => 'Terms Page',
                    'label' => 'Terms page introduction',
                    'value_en' => 'Please read these terms of service carefully before using our industrial chemical solutions, technical documentation, and digital platforms.',
                    'value_ar' => 'يُرجى قراءة شروط الاستخدام بعناية قبل استخدام حلولنا الكيميائية الصناعية أو وثائقنا الفنية أو منصاتنا الرقمية.',
                ],
                [
                    'key' => 'terms_section_1_title',
                    'group' => 'Terms Page',
                    'label' => 'Terms section 1 title',
                    'value_en' => 'Service & Quality Terms',
                    'value_ar' => 'شروط الخدمة والجودة',
                ],
                [
                    'key' => 'terms_section_1_content',
                    'group' => 'Terms Page',
                    'label' => 'Terms section 1 content',
                    'value_en' => 'By accessing our services, you agree to comply with all industrial chemical standards and safety regulations specified by ACTA Chemical.',
                    'value_ar' => 'باستخدامك لخدماتنا، فإنك توافق على الالتزام بجميع المعايير الكيميائية الصناعية ولوائح السلامة التي تحددها أكتا للكيماويات.',
                ],
                [
                    'key' => 'terms_section_2_title',
                    'group' => 'Terms Page',
                    'label' => 'Terms section 2 title',
                    'value_en' => 'Product Liability & Compliance',
                    'value_ar' => 'مسؤولية المنتج والامتثال',
                ],
                [
                    'key' => 'terms_section_2_content',
                    'group' => 'Terms Page',
                    'label' => 'Terms section 2 content',
                    'value_en' => 'ACTA Chemical maintains strict laboratory quality control, and users are responsible for the proper technical handling and application of chemical products.',
                    'value_ar' => 'تحافظ أكتا للكيماويات على رقابة صارمة على الجودة المعملية، ويتحمل المستخدمون مسؤولية التداول والتطبيق الفني السليم للمنتجات الكيميائية.',
                ],
                [
                    'key' => 'terms_section_3_title',
                    'group' => 'Terms Page',
                    'label' => 'Terms section 3 title',
                    'value_en' => 'Environmental Regulations',
                    'value_ar' => 'اللوائح البيئية',
                ],
                [
                    'key' => 'terms_section_3_content',
                    'group' => 'Terms Page',
                    'label' => 'Terms section 3 content',
                    'value_en' => 'All industrial operations must comply with local and international environmental protection laws when using our chemical solutions.',
                    'value_ar' => 'يجب أن تلتزم جميع العمليات الصناعية بالقوانين المحلية والدولية لحماية البيئة عند استخدام حلولنا الكيميائية.',
                ],
                [
                    'key' => 'terms_notice_title',
                    'group' => 'Terms Page',
                    'label' => 'Terms notice title',
                    'value_en' => 'Important Safety Notice',
                    'value_ar' => 'تنبيه مهم بشأن السلامة',
                ],
                [
                    'key' => 'terms_notice_description',
                    'group' => 'Terms Page',
                    'label' => 'Terms notice description',
                    'value_en' => 'These terms are governed by industrial and laboratory regulations. Compliance with chemical safety protocols is mandatory for all clients and partners.',
                    'value_ar' => 'تخضع هذه الشروط للوائح الصناعية والمعملية، ويجب على جميع العملاء والشركاء الالتزام ببروتوكولات السلامة الكيميائية.',
                ],
            ];

            foreach ($siteContents as $siteContent) {
                SiteContent::firstOrCreate([
                    'key' => $siteContent['key'],
                ], $siteContent);
            }

            return;
        }

        $events = [
            [
                'title_en' => 'Annual Chemistry Conference 2026',
                'title_ar' => 'مؤتمر الكيمياء السنوي 2026',
                'content_en' => '<p>Join us for the annual chemistry conference where leading scientists present their latest research in organic and inorganic chemistry.</p>',
                'content_ar' => '<p>انضموا إلينا في مؤتمر الكيمياء السنوي حيث يعرض كبار العلماء أحدث أبحاثهم في الكيمياء العضوية وغير العضوية.</p>',
            ],
            [
                'title_en' => 'Lab Safety Workshop',
                'title_ar' => 'ورشة سلامة المعامل',
                'content_en' => '<p>A hands-on workshop covering essential lab safety protocols, proper handling of hazardous materials, and emergency procedures.</p>',
                'content_ar' => '<p>ورشة عملية تغطي بروتوكولات سلامة المعامل، التعامل السليم مع المواد الخطرة، وإجراءات الطوارئ.</p>',
            ],
            [
                'title_en' => 'New Product Launch: AcidPro Series',
                'title_ar' => 'إطلاق منتج جديد: سلسلة AcidPro',
                'content_en' => '<p>We are excited to announce the launch of our new AcidPro Series, offering high-purity industrial acids for manufacturing applications.</p>',
                'content_ar' => '<p>يسعدنا الإعلان عن إطلاق سلسلة AcidPro الجديدة التي تقدم أحماضًا صناعية عالية النقاء لتطبيقات التصنيع.</p>',
            ],
            [
                'title_en' => 'International Trade Exhibition',
                'title_ar' => 'المعرض التجاري الدولي',
                'content_en' => '<p>ActaChemical will be exhibiting at the International Chemical Trade Exhibition. Visit our booth to explore our full product catalog.</p>',
                'content_ar' => '<p>ستشارك اكتا كيميكال في المعرض التجاري الدولي للكيماويات. زوروا جناحنا للتعرف على كتالوج المنتجات الكامل.</p>',
            ],
            [
                'title_en' => 'Research Partnership with Cairo University',
                'title_ar' => 'شراكة بحثية مع جامعة القاهرة',
                'content_en' => '<p>We are proud to announce a new research partnership with Cairo University\'s Faculty of Science to develop sustainable chemical solutions.</p>',
                'content_ar' => '<p>نفخر بالإعلان عن شراكة بحثية جديدة مع كلية العلوم بجامعة القاهرة لتطوير حلول كيميائية مستدامة.</p>',
            ],
        ];

        foreach ($events as $event) {
            Event::updateOrCreate([
                'title_en' => $event['title_en'],
            ], $event);
        }

        $articles = [
            [
                'title_en' => 'The Role of Sulfuric Acid in Modern Industry',
                'title_ar' => 'دور حمض الكبريتيك في الصناعة الحديثة',
                'content_en' => '<p>Sulfuric acid is one of the most widely produced chemicals in the world. It plays a critical role in fertilizer production, petroleum refining, wastewater processing, and chemical synthesis.</p>',
                'content_ar' => '<p>يعد حمض الكبريتيك من أكثر المواد الكيميائية إنتاجًا في العالم، وله دور مهم في صناعة الأسمدة وتكرير البترول ومعالجة المياه والتخليق الكيميائي.</p>',
            ],
            [
                'title_en' => 'Understanding Chemical Safety in the Workplace',
                'title_ar' => 'فهم السلامة الكيميائية في بيئة العمل',
                'content_en' => '<p>Workplace chemical safety is not just a regulatory requirement — it is a fundamental responsibility. Proper labeling, storage, and handling of chemicals can prevent accidents and protect workers.</p>',
                'content_ar' => '<p>السلامة الكيميائية في مكان العمل ليست مجرد متطلب تنظيمي، بل مسؤولية أساسية. يساعد التخزين والتصنيف والتداول الصحيح على تقليل المخاطر وحماية العاملين.</p>',
            ],
            [
                'title_en' => 'Green Chemistry: Toward Sustainable Solutions',
                'title_ar' => 'الكيمياء الخضراء نحو حلول مستدامة',
                'content_en' => '<p>Green chemistry focuses on designing products and processes that minimize the use and generation of hazardous substances while reducing waste and energy consumption.</p>',
                'content_ar' => '<p>تركز الكيمياء الخضراء على تصميم منتجات وعمليات تقلل استخدام وإنتاج المواد الخطرة، مع خفض المخلفات واستهلاك الطاقة.</p>',
            ],
            [
                'title_en' => 'What Is High-Purity Chemistry and Why Does It Matter?',
                'title_ar' => 'ما هي الكيماويات عالية النقاء ولماذا تهم؟',
                'content_en' => '<p>High-purity chemicals are essential in industries where even trace contaminants can compromise results, such as pharmaceuticals, semiconductors, and analytical laboratories.</p>',
                'content_ar' => '<p>الكيماويات عالية النقاء ضرورية في الصناعات التي قد تؤثر فيها الشوائب البسيطة على النتائج، مثل الأدوية وأشباه الموصلات والمعامل التحليلية.</p>',
            ],
            [
                'title_en' => 'Innovations in Industrial Chemical Packaging',
                'title_ar' => 'ابتكارات تعبئة الكيماويات الصناعية',
                'content_en' => '<p>Packaging plays a vital role in chemical logistics. The right packaging protects both the product and the people handling it.</p>',
                'content_ar' => '<p>تلعب التعبئة دورًا مهمًا في لوجستيات الكيماويات، فاختيار العبوة المناسبة يحمي المنتج والأشخاص المسؤولين عن تداوله.</p>',
            ],
        ];

        foreach ($articles as $article) {
            Article::updateOrCreate([
                'title_en' => $article['title_en'],
            ], $article);
        }

        $contacts = [
            [
                'name' => 'Ahmed Hassan',
                'email' => 'ahmed.hassan@example.com',
                'phone' => '+201112223334',
                'subject' => 'Bulk chemical supply inquiry',
                'msg' => 'I would like to request pricing and availability for bulk industrial chemicals for monthly supply.',
            ],
            [
                'name' => 'Mona Ali',
                'email' => 'mona.ali@example.com',
                'phone' => '+201055566677',
                'subject' => 'Safety data sheet request',
                'msg' => 'Please send the latest SDS documents for your high-purity acid products.',
            ],
            [
                'name' => 'Omar Kamal',
                'email' => 'omar.kamal@example.com',
                'phone' => '+201098887776',
                'subject' => 'Partnership request',
                'msg' => 'Our company is interested in discussing a distribution partnership with Acta Chemical.',
            ],
        ];

        foreach ($contacts as $contact) {
            Contact::firstOrCreate([
                'email' => $contact['email'],
                'subject' => $contact['subject'],
            ], $contact);
        }
    }
}
