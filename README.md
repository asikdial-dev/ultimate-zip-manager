# Ultimate ZIP Manager (PHP) — Chunk Upload + Extract + File Manager

Upload large ZIP files on hosting that only allows **2MB uploads** (or similar low limits).  
This tool uploads your file in **small chunks**, rebuilds it on the server, then lets you **extract**, **browse**, and **delete** files easily.

> Built for students, freelancers, and developers using free/shared hosting.

---

## Why this exists (The real problem)

Many free hosting platforms limit uploads to **2MB**.  
That makes it hard to deploy:
- WordPress backups
- Large website builds
- ZIP project folders
- SQL backups (zipped)
- Client project files

**Ultimate ZIP Manager** solves the *per-upload limit* problem using **chunked upload**.

---

## What it can do

✅ Upload large files using chunked upload (bypasses **per-request upload limits**)  
✅ Extract ZIP files on the server  
✅ Browse directories  
✅ Select & delete files/folders  
✅ Works as a **single PHP file** (easy to upload)  
✅ No database required  
✅ No frameworks required

---

## What it cannot do (important)

This tool **cannot bypass**:
- Your hosting **disk/storage quota**
- Provider **Terms of Service**
- Server-side hard blocks like WAF rules (some hosts block long uploads)
- Total bandwidth limits

If your host gives you **1GB storage**, you can’t upload **10GB**—chunk upload won’t change that.

---

## Quick Start (60 seconds)

### Step 1 — Upload the script
Upload `ultimate-zip-manager.php` to your hosting (example: `public_html/`)

### Step 2 — Open in browser
Visit:
