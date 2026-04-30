<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New reply on {{ config('app.name') }}</title>
</head>
<body style="margin:0;padding:0;background:#0f1010;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0f1010;padding:40px 16px;">
        <tr>
            <td align="center">

                {{-- Card --}}
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#1a1d1a;border:1px solid #2a2f2a;border-radius:12px;overflow:hidden;">

                    {{-- Header --}}
                    <tr>
                        <td style="padding:24px 28px 20px;border-bottom:1px solid #242824;">
                            <p style="margin:0;font-size:13px;color:#6b7a6b;letter-spacing:0.04em;text-transform:uppercase;font-weight:600;">
                                {{ config('app.name') }} &mdash; Forum
                            </p>
                            <p style="margin:8px 0 0;font-size:20px;font-weight:700;color:#e8ede8;line-height:1.3;">
                                New reply in your thread
                            </p>
                        </td>
                    </tr>

                    {{-- Reply block --}}
                    <tr>
                        <td style="padding:24px 28px;">

                            {{-- Meta row: avatar initial + name + time --}}
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="vertical-align:middle;padding-right:12px;">
                                        <div style="width:38px;height:38px;border-radius:50%;background:#3a6b3a;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:#c8e6c8;text-align:center;line-height:38px;">
                                            {{ strtoupper(substr($reply->author->name, 0, 1)) }}
                                        </div>
                                    </td>
                                    <td style="vertical-align:middle;">
                                        <p style="margin:0;font-size:15px;font-weight:700;color:#e8ede8;">
                                            {{ $reply->author->name }}
                                        </p>
                                        <p style="margin:4px 0 0;font-size:13px;color:#6b7a6b;">
                                            {{ $reply->created_at->diffForHumans() }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            {{-- Thread title --}}
                            <p style="margin:18px 0 10px;font-size:12px;color:#6b7a6b;text-transform:uppercase;letter-spacing:0.06em;font-weight:600;">
                                In &ldquo;{{ $thread->title }}&rdquo;
                            </p>

                            {{-- Reply excerpt --}}
                            <div style="background:#111511;border-left:3px solid #3a6b3a;border-radius:0 6px 6px 0;padding:14px 16px;margin:0;">
                                <p style="margin:0;font-size:15px;color:#b0bab0;line-height:1.65;">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($reply->body), 280) }}
                                </p>
                            </div>

                        </td>
                    </tr>

                    {{-- CTA button --}}
                    <tr>
                        <td style="padding:4px 28px 28px;">
                            <a href="{{ $url }}"
                               style="display:inline-block;background:#3a6b3a;color:#c8e6c8;text-decoration:none;font-size:14px;font-weight:700;padding:12px 24px;border-radius:8px;letter-spacing:0.02em;">
                                View Reply &rarr;
                            </a>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:16px 28px;border-top:1px solid #242824;">
                            <p style="margin:0;font-size:12px;color:#4a574a;line-height:1.6;">
                                You're receiving this because you own or participated in this thread.<br>
                                <a href="{{ route('forum.threads.show', [$reply->thread->category, $reply->thread]) }}" style="color:#5a8f5a;text-decoration:none;">View full thread</a>
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>
</body>
</html>
