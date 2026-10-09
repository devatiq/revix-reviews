# Trustpilot setup guide (Revix Reviews)

This guide explains how to connect **Revix Reviews** to Trustpilot so summary and review widgets work on your WordPress site.

## Why an API key is required

Trustpilot no longer allows reliable server-side HTML scraping. Requests from WordPress (`wp_remote_get`) typically receive an AWS WAF challenge page (“Verifying your connection…”) instead of review markup.

Revix therefore uses the **Trustpilot Public API**:

1. Your server calls Trustpilot with your **API key**
2. Trustpilot returns JSON (reviews + TrustScore)
3. Revix caches the response (about 12 hours)
4. Shortcodes / Elementor widgets render from that cache

Never expose the API key in browser JavaScript or public theme code.

## Prerequisites

- A [Trustpilot Business](https://business.trustpilot.com/) account for your domain
- Permission to create a **Public API** application
- Your public review page URL, for example:  
  `https://www.trustpilot.com/review/yourdomain.com`

## Step 1 — Create a Trustpilot API application

1. Sign in to Trustpilot Business.
2. Open **Integrations** / **Developers** (wording may vary by account).
3. Create a new application for the Public API.
4. Copy the **API key**.

Official docs:

- [Trustpilot API introduction](https://developers.trustpilot.com/)
- [Business Units API](https://developers.trustpilot.com/business-units-api)
- [Find a business unit](https://developers.trustpilot.com/business-units-api-overview)

## Step 2 — Find your Business Unit ID (recommended)

Almost every Trustpilot API call needs a **Business Unit ID (BUID)**.

### Option A — Paste it in Revix (recommended)

1. Call the Find endpoint with your domain (base domain, not a full path):

```bash
curl -X GET "https://api.trustpilot.com/v1/business-units/find?name=yourdomain.com" \
  -H "apikey: YOUR_API_KEY"
```

2. In the JSON response, copy the `id` field.
3. Paste it into **Reviews → Settings → Trustpilot → Business Unit ID**.

### Option B — Let Revix resolve it

1. Leave **Business Unit ID** empty.
2. Enter your full review page URL, for example:  
   `https://www.trustpilot.com/review/yourdomain.com`
3. Revix extracts the domain and calls the Find API for you.

If lookup fails, paste the BUID manually (Option A).

## Step 3 — Configure Revix

Go to:

**WordPress admin → Reviews → Settings → Trustpilot**

Fill in:

| Field | Required | Notes |
|-------|----------|--------|
| **Trustpilot API Key** | Yes | Public API key from your Trustpilot app |
| **Trustpilot Review Page URL** | Yes if BUID empty | Used to resolve the business unit |
| **Business Unit ID** | Optional | Faster and more reliable than URL lookup |

Click **Save All Settings**. Saving clears Trustpilot caches.

## Step 4 — Display reviews on the front end

### Shortcodes

```text
[revix_trustpilot_summary]
[revix_trustpilot_reviews]
```

Common attributes for reviews:

```text
[revix_trustpilot_reviews count="10" min_rating="4" max_rating="5" debug="true"]
```

| Attribute | Default | Description |
|-----------|---------|-------------|
| `count` | `15` | How many reviews to show |
| `min_rating` | `0` | Minimum stars (1–5) |
| `max_rating` | `5` | Maximum stars |
| `debug` | `false` | Show/log setup errors when `true` |

### Elementor

1. Enable Elementor widgets under **Reviews → Settings → General**.
2. Add **Trustpilot Summary** and/or **Trustpilot Reviews** from the *Revix Reviews* category.

## How data is fetched

| Widget / shortcode | API used |
|--------------------|----------|
| Summary | `GET /v1/business-units/{businessUnitId}` → TrustScore + review count |
| Reviews list | `GET /v1/business-units/{businessUnitId}/reviews` → recent public reviews |

Responses are stored in WordPress transients for about **12 hours**. Caches are cleared when you change the API key, URL, or Business Unit ID.

## Troubleshooting

### Empty Trustpilot section on the site

1. Confirm the API key is saved.
2. Confirm either a valid review URL **or** Business Unit ID is saved.
3. Confirm the business has public reviews on Trustpilot.
4. Temporarily use `debug="true"` on the reviews shortcode.
5. Check `wp-content/debug.log` for lines starting with `Revix Trustpilot`.

### HTTP 401 / 403 from the API

- Wrong or revoked API key
- Key from a different Trustpilot product / environment
- Create a new Public API application and update the key in Revix

### Business unit not found

- Domain in the URL must match Trustpilot’s identifying name  
  (try `yourdomain.com` vs `www.yourdomain.com`)
- Prefer pasting the Business Unit ID from the Find API response

### Still seeing old empty output

- Save Trustpilot settings again (clears cache)
- Purge any full-page cache / CDN covering the demo page
- Confirm the updated plugin version is deployed (API-based fetcher, not HTML scrape)

## Security notes

- Store the API key only in WordPress options (Revix settings).
- Do not commit API keys to Git.
- Do not print the key in page HTML or browser consoles.
- Use a dedicated Trustpilot application for production when possible.

## Related links

- Plugin settings: **Reviews → Settings → Trustpilot**
- [Trustpilot Developers](https://developers.trustpilot.com/)
- [Create a custom TrustBox using APIs](https://help.trustpilot.com/s/article/Create-a-custom-TrustBox-widget-using-Trustpilot-APIs)
