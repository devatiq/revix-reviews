# 🚀 Quick Fix for Revix Reviews Display Issues

## ✅ Step-by-Step Checklist

### 1️⃣ Test with Debug Mode

Replace your shortcodes with these debug versions:

```
[revix_trustpilot_reviews debug="true"]
[revix_google_reviews debug="true"]
```

Refresh your page and check what error messages appear.

---

### 2️⃣ For Trustpilot Issues

**From your log, you fetched 19 reviews successfully!** ✓

The issue is likely one of these:

#### A. Reviews have no text
- Some Trustpilot reviews might be empty (just ratings, no written review)
- The shortcode only displays reviews that have text
- **Solution**: This is normal behavior

#### B. JavaScript not loading
1. Check browser console (F12 → Console tab) for errors
2. Verify the JS file is loading: View page source and search for `trustpilot.js`
3. Clear your browser cache

#### C. CSS hiding the reviews
1. Check if reviews container exists: View page source and search for `revix-trustpilot-reviews`
2. Check if it has `display:none` - JavaScript should change this to `display:grid`
3. Temporarily add this CSS to force display:
   ```css
   .revix-trustpilot-reviews { display: grid !important; }
   ```

---

### 3️⃣ For Google Reviews Issues

Check these in order:

1. **API Key is set?**
   - Go to: WP Admin → Revix Reviews → Google tab
   - Make sure API Key field has a value (starts with `AIza...`)

2. **Place ID is set?**
   - Same location as above
   - Should look like: `ChIJN1t_tDeuEmsRUsoyG83frY4`

3. **Places API is enabled?**
   - Visit: [Google Cloud Console](https://console.cloud.google.com/apis/library/places-backend.googleapis.com)
   - Click "ENABLE" if not already enabled

4. **API Key has no restrictions blocking it?**
   - Go to: [API Credentials](https://console.cloud.google.com/apis/credentials)
   - Click on your API key
   - Either set "Application restrictions" to "None" OR add your website domain
   - Make sure "Places API" is in the list of allowed APIs

---

### 4️⃣ Quick Browser Console Test

Open browser console (F12) and paste this:

```javascript
// Check if Trustpilot elements exist
console.log('Loader:', document.querySelector('.revix-loader-wrapper'));
console.log('Reviews container:', document.querySelector('.revix-trustpilot-reviews'));
console.log('Review items:', document.querySelectorAll('.revix-trustpilot-single-review').length);

// Force display
var reviews = document.querySelector('.revix-trustpilot-reviews');
if(reviews) reviews.style.display = 'grid';
```

This will:
- Show if the elements are in the page
- Show how many reviews are rendered
- Force display the reviews if they're hidden

---

### 5️⃣ Use the Debug Page

1. Visit: `yoursite.com/wp-content/plugins/revix-reviews/debug-reviews.php`
2. This page will show you:
   - ✓ Which shortcodes are registered
   - ✓ Configuration status
   - ✓ Actual data being fetched
   - ✓ Live shortcode output

---

## 🎯 Most Likely Causes

Based on your log showing **19 reviews fetched**:

1. **Reviews filtered out** - Reviews without text won't display
2. **JavaScript not running** - Loader stays visible, reviews stay hidden
3. **CSS file not loading** - Check Network tab in browser DevTools

---

## 🔧 Immediate Actions

Try these RIGHT NOW:

### Option A: Force Display (Temporary Test)
Add this to your page/post (in HTML mode):

```html
<style>
.revix-trustpilot-reviews { display: grid !important; }
.revix-loader-wrapper { display: none !important; }
</style>
[revix_trustpilot_reviews count="5"]
```

If reviews appear, the issue is with JavaScript.

### Option B: Check Page Source
1. View page source (Ctrl+U or Cmd+U)
2. Search for: `revix-trustpilot-single-review`
3. If you find it, reviews are being rendered but hidden
4. If you don't find it, reviews are being filtered out

### Option C: Simplify the Test
Create a fresh test page with ONLY this content:

```
[revix_trustpilot_reviews count="3" min_rating="0" debug="true"]
```

This removes any theme/plugin conflicts.

---

## 📊 Expected vs Actual

| What Should Happen | Check This |
|-------------------|------------|
| Fetches reviews | ✅ WORKING (19 reviews) |
| Filters reviews with text | ❓ Need to verify |
| Renders HTML | ❓ Check page source |
| Hides loader | ❓ Check if JS runs |
| Shows reviews | ❌ NOT WORKING |

---

## 🆘 Report Back

After trying the above, tell me:

1. **Page source check**: Do you see `<div class="revix-trustpilot-single-review">` in the page source?
2. **Browser console**: Any JavaScript errors?
3. **Debug mode**: What message does `debug="true"` show?
4. **Count**: How many `revix-trustpilot-single-review` divs are in the page?

This will tell us exactly where the issue is!
