# Sora Studio — Text to Video

A PHP + JS front-end for OpenAI's Sora 2 video generation API. The API key
stays on the server — the browser never sees it.

## Setup

1. Open `config/config.php` and paste your OpenAI API key:
   ```php
   define('OPENAI_API_KEY', 'sk-...');
   ```
2. Upload the whole `sora-studio` folder to any PHP host (needs PHP 7.4+
   with cURL enabled — same as your Cute Markhor Store hosting works fine).
3. Visit `index.html` in your browser.

## How it works

- `index.html` + `js/script.js` — the clapperboard prompt form and the
  "dailies" gallery where results appear.
- `api/create_video.php` — starts a generation job (`POST /v1/videos`).
- `api/check_status.php` — the frontend polls this every 3s until the job
  is `completed` or `failed`.
- `api/get_video.php` — once completed, this proxies the video bytes from
  OpenAI to the browser (and caches them in `storage/` so you don't
  re-download on refresh).

## Notes

- **Check your OpenAI dashboard** for exact Sora 2 pricing per second/size
  before generating a lot of clips — video gen is billed differently than
  text.
- If OpenAI changes the exact endpoint paths (`/v1/videos`,
  `/v1/videos/{id}`, `/v1/videos/{id}/content`) as the API matures out of
  preview, update those three paths in `config/config.php` /
  `api/get_video.php` — everything else stays the same.
- `storage/` and `config/` both have `.htaccess` files blocking direct web
  access — keep those in place when you upload.
- Generation isn't instant — expect anywhere from ~30s to a few minutes
  depending on duration/model, which is why the UI polls and shows a
  progress bar instead of blocking.
