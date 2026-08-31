<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Event;
use App\Models\Setting;
use App\Models\SiteContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    /** @unauthenticated */
    public function index(Request $request)
    {
        $locale = $this->localeFromRequest($request);
        $setting = Setting::first();
        $events = Event::orderByDesc('id')
            ->get()
            ->map(function ($item) use ($locale) {
                return [
                    'id' => $item->id,
                    'title' => $item->localized('title', $locale),
                    'image' => $item->image_url,
                    'date' => $item->created_at->format('Y-m-d'),
                    'time' => $item->created_at->format('H:i A'),
                ];
            });
        $articles = Article::orderByDesc('id')
            ->get()
            ->map(function ($item) use ($locale) {
                return [
                    'id' => $item->id,
                    'title' => $item->localized('title', $locale),
                    'image' => $item->image_url,
                    'date' => $item->created_at->format('Y-m-d'),
                    'time' => $item->created_at->format('H:i A'),
                ];
            });
        $content = Schema::hasTable('site_contents')
            ? SiteContent::query()
                ->orderBy('key')
                ->get()
                ->reduce(function (array $content, SiteContent $item) use ($locale): array {
                    $content[$item->key] = $item->localizedValue($locale);

                    if (str_ends_with($item->key, '_image') || filled($item->image)) {
                        $content["{$item->key}_url"] = $item->image_url;
                    }

                    return $content;
                }, [])
            : [];

        return response()->json([
            'success' => true,
            'data' => [
                'setting' => $setting ? $this->settingResponse($setting, $locale) : null,
                'content' => $content,
                'events' => $events,
                'articles' => $articles,
            ],
        ]);
    }

    /** @unauthenticated */
    public function show_event(Request $request, Event $event)
    {
        $locale = $this->localeFromRequest($request);

        return response()->json([
            'success' => true,
            'data' => $this->contentResponse($event, $locale),
        ]);
    }

    /** @unauthenticated */
    public function show_article(Request $request, Article $article)
    {
        $locale = $this->localeFromRequest($request);

        return response()->json([
            'success' => true,
            'data' => $this->contentResponse($article, $locale),
        ]);
    }

    private function localeFromRequest(Request $request): string
    {
        $locale = $request->query('lang', $request->header('Accept-Language', 'en'));

        return str_starts_with(strtolower($locale), 'ar') ? 'ar' : 'en';
    }

    private function settingResponse(Setting $setting, string $locale): array
    {
        return [
            'id' => $setting->id,
            'title' => $setting->localized('title', $locale),
            'description' => $setting->localized('description', $locale),
            'watts' => $setting->watts,
            'email' => $setting->email,
            'phone' => $setting->phone,
            'logo' => $setting->logo,
            'logo_url' => $setting->logo_url,
            'lat' => $setting->lat,
            'lng' => $setting->lng,
            'map' => $setting->map,
            'address' => $setting->address,
        ];
    }

    private function contentResponse(Event|Article $item, string $locale): array
    {
        return [
            'id' => $item->id,
            'title' => $item->localized('title', $locale),
            'content' => $item->localized('content', $locale),
            'image' => $item->image,
            'image_url' => $item->image_url,
            'date' => $item->created_at->format('Y-m-d'),
            'time' => $item->created_at->format('H:i A'),
            'created_at' => $item->created_at,
            'updated_at' => $item->updated_at,
        ];
    }
}
