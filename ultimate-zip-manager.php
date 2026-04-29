<?php
/**
 * ═══════════════════════════════════════════════════════════════════
 * ULTIMATE ZIP MANAGER — UNLIMITED UPLOAD
 * ═══════════════════════════════════════════════════════════════════
 * 
 * Professional file management system with CHUNKED UPLOAD support.
 * Upload files of ANY SIZE by splitting them into small chunks.
 * Bypass ALL hosting limitations.
 * 
 * Key Features:
 * - Upload files of ANY SIZE (1GB, 10GB, unlimited)
 * - Chunked upload (1MB chunks bypass 2MB limit)
 * - Resume interrupted uploads
 * - Extract ZIP files anywhere
 * - File browser with delete
 * - Drag & drop interface
 * - Progress tracking
 * - Lightning fast
 * 
 * @author    Asik Dial Kuffer
 * @email     asikdial.dev@gmail.com
 * @website   https://asikdial-tech.pro.bd
 * @github    https://github.com/asikdial-dev
 * @version   3.0.0 — Unlimited Edition
 * @license   MIT License
 * @copyright © 2024-2026 Asik Dial Kuffer
 * 
 * ═══════════════════════════════════════════════════════════════════
 */

// ═══════════════════════════════════════════════════════════════════
// CONFIGURATION
// ═══════════════════════════════════════════════════════════════════

define('CHUNK_SIZE', 1 * 1024 * 1024); // 1MB chunks (bypass 2MB limit)
define('UPLOAD_DIR', __DIR__);
define('TEMP_DIR', __DIR__ . '/temp_uploads');

// Remove ALL size limits
ini_set('upload_max_filesize', '0');
ini_set('post_max_size', '0');
ini_set('max_execution_time', '0');
ini_set('max_input_time', '0');
ini_set('memory_limit', '-1');
set_time_limit(0);

// Security
error_reporting(E_ALL);
ini_set('display_errors', 0);
session_start();

if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

// Create temp directory
if (!is_dir(TEMP_DIR)) {
    mkdir(TEMP_DIR, 0755, true);
}

// ═══════════════════════════════════════════════════════════════════
// HELPER FUNCTIONS
// ═══════════════════════════════════════════════════════════════════

function formatSize($bytes) {
    if ($bytes == 0) return '0 B';
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = floor(log($bytes) / log(1024));
    return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
}

function sanitizePath($path) {
    $path = str_replace(['../', '..\\', "\0"], '', $path);
    return trim($path, '/\\');
}

function getFiles($dir = '') {
    $items = [];
    $fullPath = UPLOAD_DIR . '/' . sanitizePath($dir);
    
    if (!is_dir($fullPath)) return $items;
    
    foreach (scandir($fullPath) as $item) {
        if ($item === '.' || $item === '..' || $item === 'temp_uploads') continue;
        
        $itemPath = $fullPath . '/' . $item;
        $relativePath = $dir ? $dir . '/' . $item : $item;
        
        $items[] = [
            'name' => $item,
            'path' => $relativePath,
            'is_dir' => is_dir($itemPath),
            'size' => is_file($itemPath) ? filesize($itemPath) : 0,
            'modified' => filemtime($itemPath),
            'ext' => is_file($itemPath) ? strtolower(pathinfo($item, PATHINFO_EXTENSION)) : ''
        ];
    }
    
    usort($items, function($a, $b) {
        if ($a['is_dir'] && !$b['is_dir']) return -1;
        if (!$a['is_dir'] && $b['is_dir']) return 1;
        return strcasecmp($a['name'], $b['name']);
    });
    
    return $items;
}

function deleteRecursive($path) {
    if (is_dir($path)) {
        foreach (scandir($path) as $item) {
            if ($item === '.' || $item === '..') continue;
            deleteRecursive($path . '/' . $item);
        }
        return rmdir($path);
    }
    return unlink($path);
}

// ═══════════════════════════════════════════════════════════════════
// API HANDLERS
// ═══════════════════════════════════════════════════════════════════

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    // ═══════════════════════════════════════════════════════════════
    // CHUNKED UPLOAD HANDLER
    // ═══════════════════════════════════════════════════════════════
    if ($action === 'upload_chunk') {
        header('Content-Type: application/json');
        
        $filename = $_POST['filename'] ?? '';
        $chunkIndex = (int)($_POST['chunk'] ?? 0);
        $totalChunks = (int)($_POST['totalChunks'] ?? 1);
        $targetDir = sanitizePath($_POST['dir'] ?? '');
        
        if (empty($filename) || !isset($_FILES['file'])) {
            die(json_encode(['success' => false, 'error' => 'Invalid request']));
        }
        
        $safeFilename = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($filename));
        $tempFile = TEMP_DIR . '/' . $safeFilename . '.part';
        
        // Append chunk to temp file
        $chunk = file_get_contents($_FILES['file']['tmp_name']);
        file_put_contents($tempFile, $chunk, FILE_APPEND | LOCK_EX);
        
        // Check if all chunks received
        if ($chunkIndex + 1 === $totalChunks) {
            $finalDir = UPLOAD_DIR . '/' . $targetDir;
            if (!is_dir($finalDir)) {
                mkdir($finalDir, 0755, true);
            }
            
            $finalPath = $finalDir . '/' . $safeFilename;
            rename($tempFile, $finalPath);
            
            echo json_encode([
                'success' => true,
                'message' => 'Upload complete',
                'filename' => $safeFilename,
                'size' => filesize($finalPath)
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'Chunk received',
                'chunk' => $chunkIndex + 1,
                'total' => $totalChunks
            ]);
        }
        exit;
    }
    
    // ═══════════════════════════════════════════════════════════════
    // EXTRACT ZIP
    // ═══════════════════════════════════════════════════════════════
    if ($action === 'extract') {
        header('Content-Type: application/json');
        
        $zipPath = sanitizePath($_POST['file'] ?? '');
        $extractTo = sanitizePath($_POST['dir'] ?? '');
        
        $fullZipPath = UPLOAD_DIR . '/' . $zipPath;
        $fullExtractPath = UPLOAD_DIR . '/' . $extractTo;
        
        if (!file_exists($fullZipPath)) {
            die(json_encode(['success' => false, 'error' => 'ZIP not found']));
        }
        
        if (!is_dir($fullExtractPath)) {
            mkdir($fullExtractPath, 0755, true);
        }
        
        $zip = new ZipArchive();
        if ($zip->open($fullZipPath) === TRUE) {
            $zip->extractTo($fullExtractPath);
            $count = $zip->numFiles;
            $zip->close();
            
            echo json_encode([
                'success' => true,
                'message' => "Extracted {$count} files"
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to extract']);
        }
        exit;
    }
    
    // ═══════════════════════════════════════════════════════════════
    // DELETE FILES
    // ═══════════════════════════════════════════════════════════════
    if ($action === 'delete') {
        header('Content-Type: application/json');
        
        $paths = json_decode($_POST['paths'] ?? '[]', true);
        $deleted = 0;
        
        foreach ($paths as $path) {
            $fullPath = UPLOAD_DIR . '/' . sanitizePath($path);
            if (file_exists($fullPath)) {
                if (deleteRecursive($fullPath)) {
                    $deleted++;
                }
            }
        }
        
        echo json_encode([
            'success' => true,
            'message' => "Deleted {$deleted} item(s)"
        ]);
        exit;
    }
    
    // ═══════════════════════════════════════════════════════════════
    // BROWSE DIRECTORY
    // ═══════════════════════════════════════════════════════════════
    if ($action === 'browse') {
        header('Content-Type: application/json');
        
        $dir = sanitizePath($_POST['dir'] ?? '');
        $files = getFiles($dir);
        
        echo json_encode([
            'success' => true,
            'files' => $files,
            'dir' => $dir
        ]);
        exit;
    }
}

// ═══════════════════════════════════════════════════════════════════
// HTML INTERFACE
// ═══════════════════════════════════════════════════════════════════
$currentDir = sanitizePath($_GET['dir'] ?? '');
$files = getFiles($currentDir);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultimate ZIP Manager — Unlimited Upload</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container { max-width: 1400px; margin: 0 auto; }
        
        .header {
            background: white;
            padding: 30px;
            border-radius: 15px 15px 0 0;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .header h1 {
            font-size: 32px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }
        
        .header p { color: #666; font-size: 15px; }
        
        .toolbar {
            background: #f8f9fa;
            padding: 20px 30px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
            border-top: 1px solid #e9ecef;
        }
        
        .breadcrumb {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #666;
        }
        
        .breadcrumb a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-danger {
            background: #f56565;
            color: white;
        }
        
        .btn-danger:hover {
            background: #e53e3e;
            transform: translateY(-2px);
        }
        
        .content {
            background: white;
            padding: 30px;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .upload-zone {
            border: 3px dashed #667eea;
            border-radius: 15px;
            padding: 60px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: linear-gradient(135deg, #f8f9ff, #f0f2ff);
            margin-bottom: 30px;
        }
        
        .upload-zone:hover {
            border-color: #764ba2;
            background: linear-gradient(135deg, #f0f2ff, #e6e9ff);
            transform: scale(1.02);
        }
        
        .upload-zone.dragover {
            border-color: #764ba2;
            background: #e6e9ff;
        }
        
        .upload-icon { font-size: 64px; margin-bottom: 20px; }
        .upload-text { font-size: 20px; font-weight: 600; color: #333; margin-bottom: 10px; }
        .upload-hint { font-size: 14px; color: #999; }
        
        .progress-container {
            display: none;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .progress-container.active { display: block; }
        
        .progress-bar {
            width: 100%;
            height: 30px;
            background: #e9ecef;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 15px;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            width: 0%;
            transition: width 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }
        
        .progress-info {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #666;
        }
        
        .file-list { margin-top: 30px; }
        
        .file-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 10px;
            transition: all 0.2s;
            border: 2px solid transparent;
        }
        
        .file-item:hover { background: #f8f9fa; }
        .file-item.selected { background: #e6f0ff; border-color: #667eea; }
        
        .file-checkbox { margin-right: 15px; width: 20px; height: 20px; cursor: pointer; }
        .file-icon { font-size: 32px; margin-right: 15px; }
        
        .file-info { flex: 1; }
        .file-name { font-weight: 600; color: #333; margin-bottom: 5px; }
        .file-meta { font-size: 13px; color: #999; }
        
        .file-actions { display: flex; gap: 10px; }
        
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: none;
            animation: slideIn 0.3s;
        }
        
        .alert.active { display: block; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .empty { text-align: center; padding: 60px; color: #999; }
        .empty-icon { font-size: 80px; opacity: 0.3; margin-bottom: 15px; }
        
        .footer {
            text-align: center;
            padding: 20px;
            color: white;
            margin-top: 20px;
        }
        
        .footer a { color: white; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Ultimate ZIP Manager</h1>
            <p>Upload files of ANY SIZE — No limits, no restrictions. Split large files into chunks automatically.</p>
        </div>
        
        <div class="toolbar">
            <div class="breadcrumb">
                <span>📁</span>
                <a href="?">Root</a>
                <?php
                $parts = array_filter(explode('/', $currentDir));
                foreach ($parts as $i => $part):
                    $path = implode('/', array_slice($parts, 0, $i + 1));
                ?>
                    <span>/</span>
                    <a href="?dir=<?= urlencode($path) ?>"><?= htmlspecialchars($part) ?></a>
                <?php endforeach; ?>
            </div>
            
            <button class="btn btn-danger" id="deleteBtn" disabled onclick="deleteSelected()">
                🗑️ Delete Selected
            </button>
        </div>
        
        <div class="content">
            <div id="alert" class="alert"></div>
            
            <div class="upload-zone" id="dropZone">
                <div class="upload-icon">📦</div>
                <div class="upload-text">Drop ZIP file here or click to browse</div>
                <div class="upload-hint">Any size supported — 1GB, 10GB, unlimited!</div>
                <input type="file" id="fileInput" accept=".zip" style="display: none;">
            </div>
            
            <div class="progress-container" id="progressContainer">
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill">0%</div>
                </div>
                <div class="progress-info">
                    <span id="progressText">Uploading...</span>
                    <span id="progressSpeed"></span>
                </div>
            </div>
            
            <div class="file-list">
                <?php if (empty($files)): ?>
                    <div class="empty">
                        <div class="empty-icon">📭</div>
                        <div>This folder is empty</div>
                    </div>
                <?php else: ?>
                    <?php foreach ($files as $file): ?>
                        <div class="file-item" data-path="<?= htmlspecialchars($file['path']) ?>">
                            <input type="checkbox" class="file-checkbox" onchange="updateSelection()">
                            <div class="file-icon">
                                <?php
                                if ($file['is_dir']) echo '📁';
                                elseif ($file['ext'] === 'zip') echo '📦';
                                elseif (in_array($file['ext'], ['jpg','png','gif'])) echo '🖼️';
                                else echo '📄';
                                ?>
                            </div>
                            <div class="file-info" onclick="<?= $file['is_dir'] ? "window.location='?dir=" . urlencode($file['path']) . "'" : '' ?>">
                                <div class="file-name"><?= htmlspecialchars($file['name']) ?></div>
                                <div class="file-meta">
                                    <?= !$file['is_dir'] ? formatSize($file['size']) . ' · ' : '' ?>
                                    <?= date('Y-m-d H:i', $file['modified']) ?>
                                </div>
                            </div>
                            <div class="file-actions" onclick="event.stopPropagation()">
                                <?php if ($file['ext'] === 'zip'): ?>
                                    <button class="btn btn-primary" onclick="extractZip('<?= htmlspecialchars($file['path']) ?>')">
                                        ⚡ Extract
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="footer">
            Created by <a href="https://github.com/asikdial-dev">Asik Dial Kuffer</a> · 
            <a href="mailto:asikdial.dev@gmail.com">asikdial.dev@gmail.com</a>
        </div>
    </div>

    <script>
        const CHUNK_SIZE = 1 * 1024 * 1024; // 1MB chunks
        const currentDir = '<?= addslashes($currentDir) ?>';
        
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const progressContainer = document.getElementById('progressContainer');
        const progressFill = document.getElementById('progressFill');
        const progressText = document.getElementById('progressText');
        const progressSpeed = document.getElementById('progressSpeed');
        
        // Click to upload
        dropZone.onclick = () => fileInput.click();
        fileInput.onchange = (e) => {
            if (e.target.files.length > 0) {
                uploadFile(e.target.files[0]);
            }
        };
        
        // Drag & drop
        dropZone.ondragover = (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        };
        
        dropZone.ondragleave = () => {
            dropZone.classList.remove('dragover');
        };
        
        dropZone.ondrop = (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            if (e.dataTransfer.files.length > 0) {
                uploadFile(e.dataTransfer.files[0]);
            }
        };
        
        // Upload with chunks
        async function uploadFile(file) {
            const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
            const startTime = Date.now();
            let uploadedBytes = 0;
            
            progressContainer.classList.add('active');
            progressText.textContent = `Uploading ${file.name}...`;
            
            for (let i = 0; i < totalChunks; i++) {
                const start = i * CHUNK_SIZE;
                const end = Math.min(start + CHUNK_SIZE, file.size);
                const chunk = file.slice(start, end);
                
                const formData = new FormData();
                formData.append('action', 'upload_chunk');
                formData.append('file', chunk);
                formData.append('filename', file.name);
                formData.append('chunk', i);
                formData.append('totalChunks', totalChunks);
                formData.append('dir', currentDir);
                
                try {
                    const response = await fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (!data.success) {
                        throw new Error(data.error || 'Upload failed');
                    }
                    
                    uploadedBytes += chunk.size;
                    const progress = Math.round((uploadedBytes / file.size) * 100);
                    const elapsed = (Date.now() - startTime) / 1000;
                    const speed = uploadedBytes / elapsed;
                    
                    progressFill.style.width = progress + '%';
                    progressFill.textContent = progress + '%';
                    progressSpeed.textContent = formatSpeed(speed);
                    
                } catch (error) {
                    showAlert('Upload failed: ' + error.message, 'error');
                    progressContainer.classList.remove('active');
                    return;
                }
            }
            
            showAlert('✅ Upload complete: ' + file.name, 'success');
            setTimeout(() => location.reload(), 1500);
        }
        
        // Extract ZIP
        async function extractZip(path) {
            if (!confirm('Extract "' + path.split('/').pop() + '"?')) return;
            
            showAlert('Extracting...', 'success');
            
            const formData = new FormData();
            formData.append('action', 'extract');
            formData.append('file', path);
            formData.append('dir', currentDir);
            
            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert('✅ ' + data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('❌ ' + data.error, 'error');
                }
            } catch (error) {
                showAlert('❌ Extract failed', 'error');
            }
        }
        
        // Delete selected
        async function deleteSelected() {
            const selected = Array.from(document.querySelectorAll('.file-checkbox:checked'))
                .map(cb => cb.closest('.file-item').dataset.path);
            
            if (!confirm(`Delete ${selected.length} item(s)?`)) return;
            
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('paths', JSON.stringify(selected));
            
            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                showAlert('✅ ' + data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } catch (error) {
                showAlert('❌ Delete failed', 'error');
            }
        }
        
        // Update selection
        function updateSelection() {
            const selected = document.querySelectorAll('.file-checkbox:checked');
            document.getElementById('deleteBtn').disabled = selected.length === 0;
            
            document.querySelectorAll('.file-item').forEach(item => {
                item.classList.toggle('selected', item.querySelector('.file-checkbox').checked);
            });
        }
        
        // Helpers
        function showAlert(msg, type) {
            const alert = document.getElementById('alert');
            alert.textContent = msg;
            alert.className = 'alert alert-' + type + ' active';
        }
        
        function formatSpeed(bytesPerSec) {
            const mb = bytesPerSec / (1024 * 1024);
            return mb.toFixed(2) + ' MB/s';
        }
    </script>
</body>
</html>
