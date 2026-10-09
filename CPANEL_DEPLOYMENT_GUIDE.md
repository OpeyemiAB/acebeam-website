# ACEBEAM ENGINEERING LIMITED - cPanel Deployment Instructions

This folder (`acebeam_new_website`) contains the complete, retouched, and modernized website for **Acebeam Engineering Limited (RC 1885530)**.

---

## How to Deploy to cPanel WITHOUT Deleting the Existing Website

Follow these simple steps in your cPanel dashboard:

### Step 1: Ready-to-Upload Package Already Prepared!
We have already built the complete, optimized zip archive for you at:
📁 `C:\Users\hp\Desktop\ACEBEAMEL\acebeam_website_cpanel_upload.zip`

You do not even need to zip anything manually. You can upload this pre-packaged zip directly to cPanel.

### Step 2: Archive the Old Site Files on cPanel (Safe Backup)
1. Log in to your **cPanel**.
2. Click **File Manager** and double-click to open `public_html`.
3. Click the **+ Folder** button at the top and name the new folder: `old_site_backup_2026`.
4. Select the old files currently in `public_html` (`index.html`, `contact.html`, `styleme.css`, `images`, etc.) and drag or move them into `old_site_backup_2026`.
   > **Note:** The old website is now 100% safely archived inside `old_site_backup_2026`. Nothing has been deleted!

### Step 3: Upload and Extract the New Website
1. While still inside `public_html`, click the **Upload** button at the top toolbar.
2. Select your `acebeam_upload.zip` file and wait for the upload bar to turn green (100%).
3. Return to `public_html`, right-click `acebeam_upload.zip`, and click **Extract**.
4. If the files extracted into a subfolder named `acebeam_new_website`, simply open that subfolder, click **Select All**, and **Move** them directly into `public_html`.
5. You can now delete the temporary `.zip` file.

### Step 4: Verify the New Website Live!
Open your browser and visit:
👉 **[https://acebeamel.com](https://acebeamel.com)**

Everyone searching and visiting your domain will now see the new, retouched engineering website!

---

## Summary of Files in this Package
* **`index.html`**: The modern, responsive flagship website featuring all 9 Engineering Divisions, Corporate Profile, Real Project Showcases, and RFQ form.
* **`assets/css/style.css`**: Official brand styling (Comfortaa + Montserrat, Acebeam Turquoise `#62C6C2`, Acebeam Green `#9DD089`).
* **`assets/js/main.js`**: Interactive menu, scroll effects, and AJAX RFQ submission handler.
* **`assets/images/`**: High-res brand logo, favicon, and authentic real engineering photography across all 9 disciplines (no AI illustrations).
* **`mail.php`**: Server-side contact and quotation processor for cPanel with anti-bot honeypot and client auto-responder.
* **`.htaccess`**: Production Apache rules for HTTPS redirect, Gzip compression, and caching.
