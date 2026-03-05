<?php
require_once __DIR__ . '/../config.php';
$page = 'office';
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ofisimiz | Erguvan Psikoloji</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #1D2D50;
            --secondary: #915F78;
            --luxe-bg: #fcfafb;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
        }

        .section {
            padding: 80px 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-family: 'Prata', serif;
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .office-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
        }

        .office-card img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .office-grid {
                grid-template-columns: 1fr;
            }

            .section-title h2 {
                font-size: 2rem;
            }
        }

        .back-nav {
            padding: 20px 0;
            background: white;
            border-bottom: 1px solid #eee;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--primary);
            font-weight: 600;
        }
    </style>
</head>

<body>
    <nav class="back-nav">
        <div class="container">
            <a href="../index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Ana Sayfaya Dön</a>
        </div>
    </nav>

    <section class="section" id="office" style="background-color: var(--luxe-bg);">
        <div class="container">
            <div class="section-title">
                <h2>Ofisimiz</h2>
                <p>Huzurlu, güvenli ve profesyonel bir terapi ortamı.</p>
            </div>
            <div class="office-grid">
                <div class="office-card"><img src="../assets/images/office/ofis-1.jpg" alt="Ofisimiz 1"></div>
                <div class="office-card"><img src="../assets/images/office/office2.jpg" alt="Ofisimiz 2"></div>
                <div class="office-card"><img src="../assets/images/office/office3.jpg" alt="Ofisimiz 3"></div>
                <div class="office-card"><img src="../assets/images/office/office4.jpg" alt="Ofisimiz 4"></div>
            </div>
        </div>
    </section>

    <footer style="background: var(--primary); color: white; padding: 40px 0; text-align: center;">
        <div class="container">
            <p>&copy;
                <?php echo date('Y'); ?> Erguvan Psikoloji. Tüm hakları saklıdır.
            </p>
        </div>
    </footer>
</body>

</html>