```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peptide Store | Premium Research Products</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #17221e;
            background: #ffffff;
            line-height: 1.6;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .announcement {
            background: #123d32;
            color: #ffffff;
            text-align: center;
            padding: 10px 15px;
            font-size: 12px;
            letter-spacing: 1px;
        }

        header {
            border-bottom: 1px solid #e8ece9;
            background: #ffffff;
        }

        .navbar {
            max-width: 1200px;
            margin: auto;
            min-height: 85px;
            padding: 18px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 25px;
        }

        .logo {
            font-size: 25px;
            font-weight: 800;
            letter-spacing: 2px;
            color: #123d32;
            white-space: nowrap;
        }

        .logo span {
            color: #8da995;
        }

        nav {
            display: flex;
            align-items: center;
            gap: 28px;
            font-size: 13px;
            font-weight: 600;
        }

        nav a:hover {
            color: #56836b;
        }

        .nav-button {
            border: 1px solid #123d32;
            padding: 11px 18px;
            white-space: nowrap;
        }

        .hero {
            background: #f0f4ef;
            padding: 85px 24px;
        }

        .hero-content {
            max-width: 1200px;
            margin: auto;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            align-items: center;
            gap: 65px;
        }

        .eyebrow {
            color: #56836b;
            text-transform: uppercase;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 2px;
            margin-bottom: 20px;
        }

        h1 {
            font-size: clamp(38px, 5vw, 62px);
            line-height: 1.12;
            letter-spacing: -2px;
            margin-bottom: 23px;
        }

        .hero-description {
            max-width: 520px;
            color: #5c6962;
            font-size: 16px;
            margin-bottom: 30px;
        }

        .button {
            display: inline-block;
            background: #123d32;
            color: #ffffff;
            padding: 15px 25px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.5px;
            transition: background 0.2s;
        }

        .button:hover {
            background: #285b49;
        }

        .hero-visual {
            min-height: 370px;
            background: linear-gradient(145deg, #d9e5d9, #a9c1ad);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 35px;
            position: relative;
            overflow: hidden;
        }

        .visual-circle {
            width: 245px;
            height: 245px;
            border: 1px solid rgba(18, 61, 50, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .visual-circle::before {
            content: "";
            position: absolute;
            width: 190px;
            height: 190px;
            border: 1px solid rgba(18, 61, 50, 0.3);
            border-radius: 50%;
        }

        .visual-text {
            text-align: center;
            color: #123d32;
            position: relative;
        }

        .visual-text strong {
            display: block;
            font-size: 32px;
            letter-spacing: 5px;
        }

        .visual-text span {
            font-size: 10px;
            letter-spacing: 3px;
        }

        .trust-strip {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            max-width: 1200px;
            margin: auto;
            padding: 25px 24px;
            gap: 20px;
            text-align: center;
            border-bottom: 1px solid #e8ece9;
        }

        .trust-item {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .trust-item span {
            display: block;
            font-size: 11px;
            font-weight: 400;
            color: #748078;
            margin-top: 4px;
        }

        .section {
            max-width: 1200px;
            margin: auto;
            padding: 75px 24px;
        }

        .section-heading {
            text-align: center;
            margin-bottom: 38px;
        }

        .section-heading h2 {
            font-size: 34px;
            letter-spacing: -1px;
            margin-bottom: 10px;
        }

        .section-heading p {
            color: #718078;
            font-size: 14px;
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
        }

        .category-card {
            background: #f1f4f0;
            min-height: 190px;
            padding: 30px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            border: 1px solid #e5eae4;
        }

        .category-card .number {
            color: #7a9b83;
            font-size: 12px;
            margin-bottom: auto;
        }

        .category-card h3 {
            font-size: 20px;
            margin-bottom: 7px;
        }

        .category-card p {
            color: #65736a;
            font-size: 13px;
        }

        .products-section {
            background: #fafbf9;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .product-card {
            background: #ffffff;
            border: 1px solid #e7ebe6;
        }

        .product-image {
            height: 210px;
            background: #edf1ec;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #63816b;
            font-size: 12px;
            letter-spacing: 2px;
        }

        .product-info {
            padding: 22px;
        }

        .product-info h3 {
            font-size: 17px;
            margin-bottom: 8px;
        }

        .product-info p {
            color: #778078;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .product-link {
            display: inline-block;
            color: #123d32;
            font-size: 12px;
            font-weight: 700;
            border-bottom: 1px solid #123d32;
            padding-bottom: 4px;
        }

        .notice {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 24px 65px;
            text-align: center;
            color: #758078;
            font-size: 12px;
        }

        footer {
            background: #123d32;
            color: #ffffff;
            padding: 45px 24px;
        }

        .footer-inner {
            max-width: 1200px;
            margin: auto;
            display: flex;
            justify-content: space-between;
            gap: 25px;
            flex-wrap: wrap;
        }

        .footer-inner p {
            color: #d1ded5;
            font-size: 12px;
            margin-top: 8px;
        }

        .footer-links {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            font-size: 12px;
        }

        @media (max-width: 800px) {
            .navbar {
                flex-wrap: wrap;
            }

            nav {
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
                gap: 15px;
            }

            .hero-content {
                grid-template-columns: 1fr;
                gap: 35px;
            }

            .hero {
                padding: 55px 20px;
            }

            .hero-visual {
                min-height: 280px;
            }

            .category-grid,
            .products-grid {
                grid-template-columns: 1fr;
            }

            .trust-strip {
                grid-template-columns: 1fr;
                gap: 18px;
            }

            .section-heading h2 {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>

    <div class="announcement">
        QUALITY • TRANSPARENCY • RESEARCH
    </div>

    <header>
        <div class="navbar">
            <a href="/" class="logo">PEPTIDE<span>STORE</span></a>

            <nav>
                <a href="#home">HOME</a>
                <a href="#categories">CATEGORIES</a>
                <a href="#products">PRODUCTS</a>
                <a href="#about">ABOUT US</a>
                <a href="#contact" class="nav-button">CONTACT</a>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero" id="home">
            <div class="hero-content">
                <div>
                    <p class="eyebrow">Welcome to Peptide Store</p>

                    <h1>Precision, Quality &amp; Research.</h1>

                    <p class="hero-description">
                        Discover our product catalogue and learn more about
                        our quality standards, product information and
                        commitment to transparency.
                    </p>

                    <a href="#categories" class="button">EXPLORE CATEGORIES</a>
                </div>

                <div class="hero-visual">
                    <div class="visual-circle">
                        <div class="visual-text">
                            <strong>PS</strong>
                            <span>QUALITY FIRST</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="trust-strip">
            <div class="trust-item">
                QUALITY FOCUSED
                <span>Product information and standards</span>
            </div>

            <div class="trust-item">
                CLEAR INFORMATION
                <span>Transparent product descriptions</span>
            </div>

            <div class="trust-item">
                CUSTOMER SUPPORT
                <span>Help when you need it</span>
            </div>
        </div>

        <section class="section" id="categories">
            <div class="section-heading">
                <p class="eyebrow">Explore Our Catalogue</p>
                <h2>Browse Categories</h2>
                <p>Find product information organised in one place.</p>
            </div>

            <div class="category-grid">
                <div class="category-card">
                    <span class="number">CATEGORY 01</span>
                    <h3>Research Products</h3>
                    <p>Explore the product catalogue.</p>
                </div>

                <div class="category-card">
                    <span class="number">CATEGORY 02</span>
                    <h3>Laboratory Supplies</h3>
                    <p>Browse available laboratory-related items.</p>
                </div>

                <div class="category-card">
                    <span class="number">CATEGORY 03</span>
                    <h3>Accessories</h3>
                    <p>Discover related products and supplies.</p>
                </div>
            </div>
        </section>

        <section class="products-section" id="products">
            <div class="section">
                <div class="section-heading">
                    <p class="eyebrow">Our Catalogue</p>
                    <h2>Featured Products</h2>
                    <p>Product cards ready for your actual catalogue.</p>
                </div>

                <div class="products-grid">
                    <article class="product-card">
                        <div class="product-image">PRODUCT IMAGE</div>
                        <div class="product-info">
                            <h3>Product One</h3>
                            <p>Add your verified product details here.</p>
                            <a href="#contact" class="product-link">ENQUIRE NOW →</a>
                        </div>
                    </article>

                    <article class="product-card">
                        <div class="product-image">PRODUCT IMAGE</div>
                        <div class="product-info">
                            <h3>Product Two</h3>
                            <p>Add your verified product details here.</p>
                            <a href="#contact" class="product-link">ENQUIRE NOW →</a>
                        </div>
                    </article>

                    <article class="product-card">
                        <div class="product-image">PRODUCT IMAGE</div>
                        <div class="product-info">
                            <h3>Product Three</h3>
                            <p>Add your verified product details here.</p>
                            <a href="#contact" class="product-link">ENQUIRE NOW →</a>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="notice" id="about">
            Product information should be accurate and appropriate for the
            intended market. Any research-use restrictions, safety information
            and legal requirements must be clearly stated where applicable.
        </section>
    </main>

    <footer id="contact">
        <div class="footer-inner">
            <div>
                <div class="logo" style="color: white;">PEPTIDE<span>STORE</span></div>
                <p>Quality, transparency and customer support.</p>
            </div>

            <div class="footer-links">
                <a href="#home">Home</a>
                <a href="#categories">Categories</a>
                <a href="#products">Products</a>
                <a href="mailto:info@example.com">Email Us</a>
            </div>

            <p>&copy; 2026 Peptide Store. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
```