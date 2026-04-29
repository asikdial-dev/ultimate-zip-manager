<div align="center">

<img src="https://capsule-render.vercel.app/api?type=waving&color=0:667eea,100:764ba2&height=200&section=header&text=Ultimate%20ZIP%20Manager&fontSize=50&fontColor=ffffff&animation=fadeIn&fontAlignY=35&desc=Upload%20Unlimited%20Files%20%E2%80%A2%20Bypass%20Hosting%20Limits&descSize=18&descAlignY=55" width="100%"/>

<br/>

[![Version](https://img.shields.io/badge/Version-3.0.0-667eea?style=for-the-badge)](https://github.com/asikdial-dev/ultimate-zip-manager)
[![License](https://img.shields.io/badge/License-MIT-764ba2?style=for-the-badge)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Stars](https://img.shields.io/github/stars/asikdial-dev/ultimate-zip-manager?style=for-the-badge&color=667eea)](https://github.com/asikdial-dev/ultimate-zip-manager/stargazers)

<br/>

**Upload files of ANY SIZE to free hosting platforms with strict upload limits.**

[Features](#-features) · [Demo](#-demo) · [Installation](#-installation) · [Usage](#-usage) · [License](#-license)

<br/>

</div>

---

## 💡 The Problem

Free hosting platforms (like InfinityFree, 000webhost, etc.) typically limit file uploads to **2MB**. This makes it nearly impossible to deploy large projects, upload databases, or transfer big ZIP files.

**Traditional solutions don't work:**
- ❌ FTP is often disabled or limited
- ❌ Split files manually = tedious
- ❌ Cloud storage = requires downloads
- ❌ SSH access = not available on free hosts

---

## ✨ The Solution

**Ultimate ZIP Manager** uses **chunked upload technology** to bypass ANY file size restriction:

- 📦 Upload files of **unlimited size** (1GB, 10GB, 100GB+)
- 🔄 Automatically splits files into **1MB chunks**
- ⚡ Reassembles chunks on the server
- 🎯 Works on **ANY** PHP hosting (even with 2MB limit)
- 🚀 Beautiful drag-and-drop interface
- 📊 Real-time progress tracking

<div align="center">

### 🎯 Upload Flow

</div>

---

## 🌟 Features

### Core Functionality

| Feature | Description |
|---------|-------------|
| 🚀 **Unlimited Upload** | Upload files of ANY size — no restrictions |
| 📦 **Chunked Technology** | Automatic splitting into 1MB chunks |
| 🎯 **Bypass Limits** | Works on hosting with 2MB upload limit |
| 📊 **Progress Tracking** | Real-time upload progress with speed indicator |
| 🎨 **Drag & Drop** | Modern drag-and-drop interface |
| ⚡ **ZIP Extraction** | Extract ZIP files with one click |
| 📂 **File Browser** | Navigate directories, view files |
| ☑️ **Multi-Select** | Select multiple files/folders for batch operations |
| 🗑️ **Batch Delete** | Delete multiple items at once |
| 🔒 **Secure** | Path traversal protection, sanitized inputs |
| 📱 **Responsive** | Works on mobile, tablet, desktop |
| 🎨 **Beautiful UI** | Modern gradient design with smooth animations |

### Technical Features

- ✅ No server configuration needed
- ✅ Single-file deployment
- ✅ Pure PHP + Vanilla JavaScript (no dependencies)
- ✅ Works with PHP 7.4+
- ✅ Automatic chunk reassembly
- ✅ Resume support (chunk-based)
- ✅ Memory-efficient streaming
- ✅ No database required

---

## 📸 Screenshots

<div align="center">

### Upload Interface

![Upload](screenshots/upload.png)

### File Browser

![Browse](screenshots/browse.png)

### Progress Tracking

![Progress](screenshots/progress.png)

</div>

---

## 🚀 Demo

**Live Demo:** [https://demo.asikdial-tech.pro.bd/zip-manager](https://demo.asikdial-tech.pro.bd/zip-manager)

**Try it yourself:**
1. Upload a large ZIP file (even 1GB+)
2. Watch real-time progress
3. Extract with one click
4. Browse and manage files

---

## 📦 Installation

### Method 1: Direct Upload (Recommended)

1. **Download** the latest release:
   ```bash
   wget https://github.com/asikdial-dev/ultimate-zip-manager/releases/latest/download/ultimate-zip-manager.php

# Step 1: Access the manager
https://yoursite.com/ultimate-zip-manager.php

# Step 2: Upload your large ZIP file
- Click upload zone OR drag & drop
- File is automatically split into chunks
- Progress bar shows real-time status

# Step 3: Extract the ZIP
- Click "Extract" button
- Files are extracted to current directory

# Step 4: Manage files
- Navigate folders
- Select files
- Delete unwanted items

# Scenario: You have a 5GB WordPress backup ZIP
# Free hosting allows max 2MB uploads

1. Open Ultimate ZIP Manager
2. Drag your 5GB file into upload zone
3. Script automatically:
   - Splits into 5000 × 1MB chunks
   - Uploads each chunk sequentially
   - Shows progress (e.g., "2.3 GB / 5 GB — 15 MB/s")
4. Once complete, click "Extract"
5. Your entire 5GB backup is now deployed!

# You have a 500MB React build

1. ZIP your build folder locally
2. Upload via Ultimate ZIP Manager
3. Extract to public_html directory
4. Delete the ZIP file
5. Your site is live!

# You have a 200MB SQL dump

1. ZIP the .sql file locally
2. Upload via manager
3. Extract
4. Import via phpMyAdmin (now it's on the server)

# 1. Rename the file
mv ultimate-zip-manager.php my-secret-uploader.php

# 2. Add .htaccess password protection
htpasswd -c .htpasswd yourusername

# .htaccess content:
AuthType Basic
AuthName "Restricted Area"
AuthUserFile /path/to/.htpasswd
Require valid-user

# 3. Delete after use
rm my-secret-uploader.php

# Check PHP max_execution_time
php -i | grep max_execution_time

# If too low, add to .htaccess:
php_value max_execution_time 0
php_value max_input_time 0

# Check if ZipArchive is enabled
php -m | grep zip

# If not available, contact your host
🤝 Contributing
Contributions are welcome! Here's how:

Fork the repository
Create a feature branch (git checkout -b feature/AmazingFeature)
Commit your changes (git commit -m 'Add AmazingFeature')
Push to the branch (git push origin feature/AmazingFeature)
Open a Pull Request
Development Guidelines
Follow PSR-12 coding standards
Add comments for complex logic
Test on multiple hosting platforms
Update documentation
🗺️ Roadmap
 Resume interrupted uploads (save chunk progress)
 Multiple file upload queue
 Download manager
 File preview (images, text)
 Compression before upload
 FTP/SFTP integration
 Cloud storage integration (S3, Dropbox)
 User authentication system
 API endpoints
 Docker image
📊 Stats
<div align="center">
GitHub stars
GitHub forks
GitHub watchers

</div>
🙏 Acknowledgments
Inspired by the frustrations of free hosting limitations
Built with passion to help developers deploy projects easily
Thanks to the open-source community
📜 License
This project is licensed under the MIT License — see the LICENSE file for details.

text

MIT License

Copyright (c) 2024-2026 Asik Dial Kuffer

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND.
👨‍💻 Author
<div align="center">
Asik Dial Kuffer

Tech Entrepreneur · Python Developer · AI & IoT Innovator

GitHub
LinkedIn
Email
Website

</div>
⭐ Show Your Support
If this project helped you, please consider giving it a ⭐ star on GitHub!

It helps others discover this tool and motivates continued development.

<div align="center">
Star this repo

Share with your network:

Twitter
Facebook
LinkedIn

</div>
<div align="center"><img src="https://capsule-render.vercel.app/api?type=waving&color=0:667eea,100:764ba2&height=120&section=footer" width="100%"/><br/>
Made with ❤️ by Asik Dial Kuffer

Empowering developers to overcome hosting limitations

<br/></div><!-- ══════════════════════════════════════════════════ Keywords for SEO ultimate zip manager, bypass upload limit, free hosting upload, chunked upload php, upload large files php, free hosting file manager, infinityfree upload limit, 000webhost bypass, upload unlimited files, php file uploader, zip extractor php, file manager php script, bypass 2mb limit, upload 1gb file free hosting, php chunked upload, asikdial-dev, asik dial kuffer, free hosting tools, web hosting tools ══════════════════════════════════════════════════ -->


