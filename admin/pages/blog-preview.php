<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Önizleme - Uzm. Psk. Sena Ceren</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #334155;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .preview-bar {
            background: #ec4899;
            color: white;
            padding: 12px 20px;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .category {
            color: #ec4899;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 0.9rem;
            margin-bottom: 15px;
            display: block;
        }

        h1 {
            font-size: 2.5rem;
            color: #1e293b;
            margin-top: 0;
            line-height: 1.2;
            font-weight: 800;
        }

        .excerpt {
            border-left: 4px solid #ec4899;
            padding-left: 20px;
            font-size: 1.2rem;
            color: #64748b;
            font-style: italic;
            margin: 30px 0;
        }

        .content {
            font-size: 1.15rem;
        }

        .content h2 {
            color: #1e293b;
            margin-top: 40px;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 10px;
        }

        .toc-box {
            background: #fdf2f8;
            border: 1px solid #fbcfe8;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 40px;
        }

        .btn-close {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid white;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="preview-bar">
        <span>👁️ SENA CEREN BLOG ÖNİZLEME (TASLAK)</span>
        <button class="btn-close" onclick="window.close()">Kapat</button>
    </div>
    <div class="container">
        <span id="p-category" class="category">PSİKOLOJİ</span>
        <h1 id="p-title">Yazı Başlığı</h1>
        <div id="p-excerpt" class="excerpt">Özet metni...</div>
        <div id="p-toc" class="toc-box" style="display:none;">
            <strong style="color:#ec4899; display:block; margin-bottom:15px;">İçindekiler</strong>
            <ul id="p-toc-list" style="list-style:none; padding:0;"></ul>
        </div>
        <div id="p-content" class="content">Yükleniyor...</div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const data = JSON.parse(localStorage.getItem('preview_data') || '{}');
            if (data.title) {
                document.getElementById('p-title').innerText = data.title;
                document.getElementById('p-excerpt').innerText = data.excerpt;
                document.getElementById('p-category').innerText = data.category || 'PSİKOLOJİ';
                document.getElementById('p-content').innerHTML = data.content;
                if (data.toc) {
                    const toc = JSON.parse(data.toc);
                    if (toc.length > 0) {
                        const list = document.getElementById('p-toc-list');
                        toc.forEach(item => {
                            const li = document.createElement('li');
                            li.innerText = item.text;
                            li.style.marginLeft = item.level === 'h3' ? '20px' : '0';
                            li.style.marginBottom = '8px';
                            list.appendChild(li);
                        });
                        document.getElementById('p-toc').style.display = 'block';
                    }
                }
            }
        });
    </script>
</body>

</html>