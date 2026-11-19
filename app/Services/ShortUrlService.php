<?php

namespace App\Services;

use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Support\Str;

class ShortUrlService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    //Create a short URL.

    public function createShortUrl(User $user, array $data)
    {
        return ShortUrl::create([
            'company_id'   => $user->company_id,
            'user_id'      => $user->id,
            'original_url' => $data['original_url'],
            'short_code'   => Str::random(8),
        ]);
    }

    // get original url from database 
    public function getOriginalUrl(string $shortCode): string
    {
        $shortUrl = ShortUrl::where('short_code', $shortCode)->firstOrFail();

        return $shortUrl->original_url;
    }

    //get short url 
    public function getShortUrls(User $user)
    {
        $query = ShortUrl::query();

        if ($user->role === User::ROLE_ADMIN) {
            $query->where('company_id', $user->company_id);
        } elseif ($user->role === User::ROLE_MEMBER) {
            $query->where('user_id', '!=', $user->id);
        }

        return $query->latest()->get();
    }
}
