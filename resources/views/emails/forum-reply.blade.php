@php
    $authorInitial = strtoupper(substr(trim($reply->author->name), 0, 1));
    $replyExcerpt = \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($reply->body))), 320);
    $threadUrl = route('forum.threads.show', [$reply->thread->category, $reply->thread]);
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New reply on {{ config('app.name') }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f1e8;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#2f352c;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f1e8;padding:40px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:580px;background:#fffaf0;border:1px solid #ddd6c7;border-radius:10px;overflow:hidden;">
                    <tr>
                        <td style="padding:26px 30px 20px;border-bottom:1px solid #e6dece;">
                            <p style="margin:0;font-size:12px;color:#728069;letter-spacing:0.12em;text-transform:uppercase;font-weight:800;">
                                {{ config('app.name') }} Forum
                            </p>
                            <h1 style="margin:10px 0 0;font-size:22px;line-height:1.3;color:#1f251d;font-weight:800;">
                                New reply in your thread
                            </h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:24px 30px 6px;">
                            <table role="presentation" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="vertical-align:middle;padding-right:12px;">
                                        <div style="width:38px;height:38px;border-radius:999px;background:#6f8f64;color:#ffffff;font-size:16px;font-weight:800;text-align:center;line-height:38px;">
                                            {{ $authorInitial }}
                                        </div>
                                    </td>
                                    <td style="vertical-align:middle;">
                                        <p style="margin:0;font-size:15px;font-weight:800;color:#1f251d;">
                                            {{ $reply->author->name }}
                                        </p>
                                        <p style="margin:4px 0 0;font-size:13px;color:#7b756a;">
                                            {{ $reply->created_at->diffForHumans() }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:22px 0 8px;font-size:12px;color:#728069;text-transform:uppercase;letter-spacing:0.08em;font-weight:800;">
                                In "{{ $thread->title }}"
                            </p>

                            <div style="background:#f7f2e7;border-left:4px solid #6f8f64;border-radius:0 8px 8px 0;padding:16px 18px;">
                                <p style="margin:0;font-size:15px;line-height:1.7;color:#3f463b;">
                                    {{ $replyExcerpt }}
                                </p>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:22px 30px 30px;">
                            <a href="{{ $url }}" style="display:inline-block;background:#2f372d;color:#ffffff;text-decoration:none;font-size:14px;font-weight:800;padding:12px 22px;border-radius:999px;">
                                View reply
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:18px 30px;border-top:1px solid #e6dece;background:#fbf6ec;">
                            <p style="margin:0;font-size:12px;line-height:1.65;color:#7b756a;">
                                You are receiving this because you own or participated in this thread.
                                <br>
                                <a href="{{ $threadUrl }}" style="color:#536e4c;text-decoration:underline;">View full thread</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
