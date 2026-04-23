<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Sense of Jewels') }} - Maintenance</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f7f1e8;
            --panel: rgba(255, 252, 247, 0.84);
            --border: rgba(151, 121, 78, 0.22);
            --text: #2c1a0e;
            --muted: rgba(44, 26, 14, 0.62);
            --accent: #bfa054;
            --accent-strong: #8c6a2f;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Nunito", "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(191, 160, 84, 0.16), transparent 32%),
                radial-gradient(circle at bottom right, rgba(140, 106, 47, 0.12), transparent 28%),
                linear-gradient(180deg, #fbf7f0 0%, var(--bg) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .shell {
            width: min(960px, 100%);
            border: 1px solid var(--border);
            background: var(--panel);
            backdrop-filter: blur(16px);
            box-shadow: 0 24px 60px rgba(44, 26, 14, 0.08);
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            overflow: hidden;
        }

        .copy {
            padding: 56px 48px;
        }

        .eyebrow {
            margin: 0 0 18px;
            font-size: 11px;
            letter-spacing: 0.38em;
            text-transform: uppercase;
            color: var(--accent-strong);
        }

        h1 {
            margin: 0;
            font-family: "Cormorant Garamond", Georgia, serif;
            font-size: clamp(42px, 7vw, 72px);
            line-height: 0.96;
            font-weight: 600;
        }

        .lead {
            margin: 22px 0 0;
            max-width: 38rem;
            font-size: 17px;
            line-height: 1.8;
            color: var(--muted);
        }

        .meta {
            margin-top: 34px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .chip {
            border: 1px solid var(--border);
            padding: 10px 14px;
            font-size: 11px;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--muted);
            background: rgba(255, 255, 255, 0.5);
        }

        .aside {
            padding: 48px 42px;
            border-left: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: linear-gradient(180deg, rgba(255,255,255,0.46) 0%, rgba(255,255,255,0.12) 100%);
        }

        .badge {
            align-self: flex-start;
            padding: 9px 12px;
            border: 1px solid rgba(140, 106, 47, 0.24);
            color: var(--accent-strong);
            background: rgba(191, 160, 84, 0.1);
            font-size: 11px;
            letter-spacing: 0.24em;
            text-transform: uppercase;
        }

        .card {
            padding-top: 42px;
        }

        .card h2 {
            margin: 0 0 12px;
            font-family: "Cormorant Garamond", Georgia, serif;
            font-size: 32px;
            font-weight: 600;
        }

        .card p {
            margin: 0;
            color: var(--muted);
            line-height: 1.8;
            font-size: 15px;
        }

        .actions {
            margin-top: 30px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            padding: 0 18px;
            border: 1px solid var(--accent);
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 0.18em;
            font-size: 11px;
            color: var(--text);
            background: transparent;
        }

        .button.primary {
            background: var(--accent);
            color: #fff;
        }

        .footer {
            margin-top: 28px;
            font-size: 12px;
            color: rgba(44, 26, 14, 0.48);
        }

        @media (max-width: 820px) {
            .shell {
                grid-template-columns: 1fr;
            }

            .aside {
                border-left: 0;
                border-top: 1px solid var(--border);
            }

            .copy,
            .aside {
                padding: 34px 24px;
            }
        }
    </style>
</head>
<body>
    <main class="shell">
        <section class="copy">
            <p class="eyebrow">Sense of Jewels</p>
            <h1>We are refining the storefront.</h1>
            <p class="lead">
                The site is temporarily unavailable while we publish updates and polish the shopping experience.
                Please check back shortly.
            </p>

            <div class="meta">
                <span class="chip">503 Service Unavailable</span>
                <span class="chip">Scheduled Maintenance</span>
            </div>

            <p class="footer">
                If you already have an order in progress, please return in a few minutes and refresh the page.
            </p>
        </section>

        <aside class="aside">
            <div class="badge">Maintenance Mode</div>

            <div class="card">
                <h2>Store access will resume soon.</h2>
                <p>
                    We are updating inventory, content, or payment flows. All customer-facing routes will reopen automatically once maintenance mode is disabled.
                </p>

                <div class="actions">
                    <a class="button primary" href="/">Refresh</a>
                    <a class="button" href="mailto:{{ config('mail.from.address', 'hello@example.com') }}">Contact Support</a>
                </div>
            </div>
        </aside>
    </main>
</body>
</html>